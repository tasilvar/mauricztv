<?php

namespace bpmj\wpidea\templates_system\templates;

use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\settings\Interface_Settings_Aware;
use bpmj\wpidea\templates_system\admin\blocks\Block;
use bpmj\wpidea\templates_system\admin\blocks\Breadcrumbs_Block;
use bpmj\wpidea\templates_system\admin\blocks\Course_Panel_Lessons_List_Block;
use bpmj\wpidea\templates_system\admin\blocks\Page_Content_Block;
use bpmj\wpidea\templates_system\admin\blocks\Page_Title_Block;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;
use bpmj\wpidea\templates_system\templates\repository\Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\translator\Interface_Translator_Aware;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\view\Interface_View_Provider_Aware;

class Template implements Interface_View_Provider_Aware, Interface_Translator_Aware, Interface_Settings_Aware
{
    protected $data;

    protected $repository;

    private $basic_blocks = [
        Breadcrumbs_Block::class,
        Page_Title_Block::class,
        Course_Panel_Lessons_List_Block::class,
        Page_Content_Block::class
    ];

    protected $registers_blocks = [];

    private ?Interface_View_Provider $view_provider = null;
    private ?Interface_Translator $translator = null;
    private ?Interface_Settings $settings = null;

    public function __construct() {
        $this->repository = new Repository();
    }

    public function init(): void
    {
        $this->register_template_blocks();
    }

    public function get_default_content()
    {
        return '';
    }

    public function is_full_page_template(): bool {
        return false;
    }

    public function get_default_name()
    {
        return __('Template', BPMJ_EDDCM_DOMAIN);
    }

    public static function default_template_exists(Template_Group_Id $group_id): ?Template
    {
        return (new static())->load_default_one_for_template_group($group_id);
    }

    public static function find_active_one_in_group(Template_Group_Id $group_id): ?Template
    {
        return (new static())->load_active_one($group_id);
    }

    public static function find($id): ?Template
    {
        return (new static())->load($id);
    }

    public static function find_all(): array
    {
        return (new static())->load_all();
    }

    public static function find_by_group(Template_Group_Id $group_id): array
    {
        return (new static())->load_all([
            ['template_group_id', $group_id->stringify()]
        ]);
    }

    public static function find_not_assigned_to_a_group(): array
    {
        return (new static)->load_all([
            ['template_group_id', null]
        ]);
    }

    public function load_all(array $where = []): array
    {
        $templates_data = $this->repository->find_all();

        $templates = [];

        foreach ($templates_data as $key => $data) {
            $all_conditions_met = true;

            /** @var Template_Data $data */
            foreach ($where as $condition) {
                if($data->{$condition[0]} !== $condition[1]) $all_conditions_met = false;
            }

            if(!$all_conditions_met) continue;

            $templates[] = self::instantiate($data);
        }

        return $templates;
    }

    public function load($id): ?Template
    {
        $data = $this->repository->find($id);

        if ($data === null) {
            return null;
        }

        return self::instantiate($data);
    }

    public function load_active_one(Template_Group_Id $group_id): ?self
    {
        $data = $this->repository->find_active(static::class, $group_id);

        if ($data === null) {
            return null;
        }

        return self::instantiate($data);
    }

    public function load_default_one_for_template_group(Template_Group_Id $group_id): ?self
    {
        $data = $this->repository->find_default(static::class, $group_id);

        if ($data === null) {
            return null;
        }

        return self::instantiate($data);
    }

    public static function create(bool $is_basic = false, bool $is_active = false, Template_Group_Id $template_group_id = null)
    {
        $template = new static;

        $tpl_data               = new Template_Data();
        $tpl_data->name         = $template->get_default_name();
        $tpl_data->content      = $template->get_default_content();
        $tpl_data->class_name   = static::class;
        $tpl_data->is_basic     = $is_basic;
        $tpl_data->is_active    = $is_active;
        $tpl_data->template_group_id = $template_group_id !== null ? $template_group_id->stringify() : null;

        return $template->set_data($tpl_data)->save();
    }

    public static function instantiate(Template_Data $data): Template
    {
        if(empty($data->class_name)) return (new self)->set_data($data);

        if(!class_exists($data->class_name)) return (new self)->set_data($data);

        return (new $data->class_name)->set_data($data);
    }

    public function save()
    {
        $stored_template_data = $this->repository->store($this->get_data());

        return $this->set_data($stored_template_data);
    }

    public function set_data(Template_Data $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function add_to_group(Template_Group_Id $id): self
    {
        $data = $this->get_data();

        $data->template_group_id = $id->stringify();

        $this->set_data($data);

        $this->save();

        return $this;
    }

    public function get_data()
    {
        return $this->data instanceof Template_Data ? $this->data : new Template_Data();
    }

    public function get_id()
    {
        return $this->get_data()->id;
    }

    public function get_name()
    {
        return $this->get_data()->name;
    }

    public function get_group_id(): ?Template_Group_Id
    {
        return Template_Group_Id::from_string($this->get_data()->template_group_id);
    }

    public function is_active()
    {
        return $this->get_data()->is_active;
    }

    public function is_basic()
    {
        return $this->get_data()->is_basic;
    }

    public function register_template_blocks()
    {
        if(empty(WPI()->templates_system) || empty(WPI()->templates_system->editor)) return;

        foreach ($this->get_registered_blocks() as $key => $block) {
            WPI()->templates_system->editor->register_block(new $block);
        }
    }

    public function init_blocks_frontend(): void
    {
        if(is_admin()) {
            return;
        }

        foreach ($this->get_registered_blocks() as $key => $block_class) {
            /** @var Block $block */
            $block = new $block_class();

            if ($this->block_already_registered($block->get_name())) {
                continue;
            }

            if($this->view_provider && ($block instanceof Interface_View_Provider_Aware)) {
                $block->set_view_provider($this->view_provider);
            }

            if($this->get_translator() && ($block instanceof Interface_Translator_Aware)) {
                $block->set_translator($this->get_translator());
            }

            if($this->settings && ($block instanceof Interface_Settings_Aware)) {
                $block->set_settings($this->settings);
            }

            $this->register_block_type($block);
        }
    }

    public function render(): string
    {
        return $this->repository->get_rendered_template_content($this->get_id());
    }

    protected function get_registered_blocks(): array
    {
        return array_merge($this->basic_blocks, $this->registers_blocks);
    }

    protected function block_already_registered($block_name): bool
    {
        return in_array($block_name, get_dynamic_block_names(), true);
    }

    protected function register_block_type(Block $block): void
    {
        register_block_type($block->get_name(), [
            'attributes' => $block->get_attributes(),
            'render_callback' => function($atts) use ($block){
                return $block->get_content_to_render($atts);
            }
        ]);
    }

    public function get_edit_url(): string
    {
        return add_query_arg([
            'post' => $this->get_id(),
            'action' => 'edit'
        ], admin_url('post.php'));
    }

    public function get_delete_url(): string
    {
        return add_query_arg([
            Template_Actions_Handler::QUERY_PARAM_RESTORE_TEMPLATE => $this->get_id(),
            Template_Actions_Handler::QUERY_PARAM_NONCE => wp_create_nonce(Template_Actions_Handler::QUERY_PARAM_RESTORE_TEMPLATE)
        ]);
    }

    public function delete(): bool
    {
        return !empty(wp_delete_post($this->get_id(), true));
    }

    public function before_render(): void
    {
        // do nothing
    }

    public function set_view_provider(Interface_View_Provider $view_provider): void
    {
        $this->view_provider = $view_provider;
    }

    public function set_translator(Interface_Translator $translator): void
    {
        $this->translator = $translator;
    }

    public function set_settings(Interface_Settings $settings): void
    {
        $this->settings = $settings;
    }

    protected function get_translator(): ?Interface_Translator
    {
        return $this->translator;
    }
}