<?php
/** @var array $tabs */

function tabbed_view_create_tab_id(string $tab_name)
{
    return strtolower(
        preg_replace('/[^A-Za-z0-9-]+/', '-', $tab_name)
    );
}
?>

<div class="tabbed-view">
    <nav class="tabbed-view__tabs-menu">
        <ul class="tabbed-view__tabs-menu__items">
        <?php foreach ($tabs as $index => $tab): ?>
            <?php
            if(isset($tab['disabled']) && $tab['disabled']){
              continue;
            }
                $is_first_tab = $index === 0;
                $tab_name = $tab['tab-name'] ?? '';
                $tab_id = $tab['tab-id'] ?? tabbed_view_create_tab_id($tab_name);
                $tab_info = $tab['tab-info'] ?? null;
                $tab_class = $tab['class'] ?? '';
            ?>
            <li class="tabbed-view__tabs-menu__tab <?= $tab_class ?>">
                <button
                    role="link"
                    class="tabbed-view__tabs-menu__tab-link <?= $is_first_tab ? 'tabbed-view__tabs-menu__tab-link--active' : '' ?>"
                    data-target-tab-id="<?= $tab_id ?>"
                >
                    <strong class="tabbed-view__tabs-menu__tab-link__title"><?= $tab_name ?></strong>

                    <?php if($tab_info): ?>
                        <span><?= $tab_info ?></span>
                    <?php endif; ?>
                </button>
            </li>
        <?php endforeach; ?>
        </ul>
    </nav>
    <div class="tabbed-view__tab-contents">
        <?php foreach ($tabs as $index => $tab): ?>
            <?php
            $is_first_tab = $index === 0;
            $tab_name = $tab['tab-name'] ?? '';
	        $tab_id = $tab['tab-id'] ?? tabbed_view_create_tab_id($tab_name);
            ?>
            <article
                class="tabbed-view__tab-content <?= $is_first_tab ? 'tabbed-view__tab-content--active' : '' ?>"
                data-tab-id="<?= $tab_id ?>"
            >
                <header>
                    <h2 class="tabbed-view__tab-content__title"><?= $tab_name ?></h2>
                </header>
                <?= $tab['tab-content'] ?? '' ?>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const tabLinkClass = 'tabbed-view__tabs-menu__tab-link';
    const tabClass = 'tabbed-view__tab-content';
    const tabLinks = document.querySelectorAll(`button[data-target-tab-id]`);

    tabLinks.forEach(tabLink => {
        tabLink.addEventListener('click', function(e){
            e.preventDefault();

            const targetTabId = this.dataset.targetTabId;

            loadTab(targetTabId);
        })
    })


    const loadTab = newTabId => {
        // update url
        const url = new URL(location.href);
        if(url.searchParams.get('autofocus') !== newTabId) {
            url.searchParams.set('autofocus', newTabId);
            window.history.pushState({ path: url.href }, '', url.href);
        }

        // change tab
        const previouslyActiveTabLink = document.querySelector(`.${tabLinkClass}--active`);

        if(previouslyActiveTabLink === this) {
            return;
        }

        previouslyActiveTabLink.classList.remove(`${tabLinkClass}--active`)
        const newActiveTabLink = document.querySelector(`.${tabLinkClass}[data-target-tab-id="${newTabId}"]`);
        newActiveTabLink.classList.add(`${tabLinkClass}--active`)

        const previouslyActiveTab = document.querySelector(`.${tabClass}--active`);
        previouslyActiveTab.classList.remove(`${tabClass}--active`)

        const targetTab = document.querySelector(`.${tabClass}[data-tab-id="${newTabId}"]`);
        targetTab.classList.add(`${tabClass}--active`)
    }

    addEventListener('popstate', event => {
        const tabInUrl = getAutofocusedTabId();

        if(!tabInUrl) {
            return;
        }

        loadTab(tabInUrl)
    });

    const getAutofocusedTabId = () => {
        const url = new URL(location.href);

        return url.searchParams.get('autofocus');
    }

    const getFirstTabId = () => {
        return document.querySelector(`.${tabClass}--active`).dataset.tabId;
    }

    let startTab = getAutofocusedTabId() ?? getFirstTabId();

    loadTab(startTab);
</script>
