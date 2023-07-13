<?php
namespace bpmj\wpidea\wolverine\repository;

use bpmj\wpidea\wolverine\product\Repository as ProductRepository;
use bpmj\wpidea\wolverine\product\course\Repository as ProductCourseRepository;
use bpmj\wpidea\learning\course\Course_Wp_Read_Only_Repository as CourseRepository;
use bpmj\wpidea\wolverine\certificate\Repository as CertificateRepository;
use Exception;

class RepositoryLocator
{
    const EX_NO_REPOSITORY_EXCEPTION = 'No repository registered for this class!';
    const EX_NOT_A_CLASS_OBJECT = 'Passed variable should be an object!';
    const EX_NOT_A_CLASS_NAME = 'Class name has to be a string!';
    const EX_NO_SUCH_REPO = 'No such repository';

    const REPO_PRODUCT = 'PRODUCT_REPOSITORY';
    const REPO_PRODUCT_COURSE = 'PRODUCT_COURSE_REPOSITORY';
    const REPO_COURSE = 'COURSE_REPOSITORY';
    const REPO_CERTIFICATE = 'CERTIFICATE_REPOSITORY';

    protected $repositoriesMap = [
        self::REPO_PRODUCT => ProductRepository::class,
        self::REPO_PRODUCT_COURSE => ProductCourseRepository::class,
        self::REPO_COURSE => CourseRepository::class,
        self::REPO_CERTIFICATE => CertificateRepository::class
    ];

    public function __construct($repositoriesMap = []) {
        $this->repositoriesMap = array_merge($this->repositoriesMap, $repositoriesMap);
    }

    public function getProductRepository()
    {
        return $this->getRepository(self::REPO_PRODUCT);
    }

    public function getCertificateRepository()
    {
        return $this->getRepository(self::REPO_CERTIFICATE);
    }

    public function getProductCourseRepository()
    {
        return $this->getRepository(self::REPO_PRODUCT_COURSE);
    }

    public function getCourseRepository()
    {
        return $this->getRepository(self::REPO_COURSE);
    }

    protected function getRepository($repoKey)
    {
        if(empty($this->repositoriesMap[$repoKey])) throw new Exception(self::EX_NO_SUCH_REPO);

        $repoClass = $this->repositoriesMap[$repoKey];

        return new $repoClass;
    }
}
