<?php

class PSR4_Autoloader {
    protected $prefixes = [];

    protected $wpStyles = [];

    public function addNamespace($prefix, $dir, $wpStyle = true)
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($dir, DIRECTORY_SEPARATOR) . '/';

        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = [];
        }

        array_push($this->prefixes[$prefix], $baseDir);

        if ($wpStyle) {
            $this->wpStyles[] = $prefix;
        }
    }

    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    public function loadClass($class)
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);

            $relativeClass = substr($class, $pos + 1);

            if (in_array($prefix, $this->wpStyles)) {
                if (strpos($relativeClass, '\\') === false) {
                    $relativeClass = $this->normalizeClassPath($relativeClass);
                } else {
                    $lastBackslashCharPosition = strrpos($relativeClass, '\\');
                    $pathString = substr($relativeClass, 0, $lastBackslashCharPosition + 1);
                    $classFileNameString = substr($relativeClass, $lastBackslashCharPosition + 1, strlen($relativeClass));

                    $relativeClass = str_replace('_', '-', $pathString) . $this->normalizeClassPath($classFileNameString);
                }
            }

            $mappedFile = $this->loadMappedFile($prefix, $relativeClass);
            if ($mappedFile) {
                return $mappedFile;
            }

            $prefix = rtrim($prefix, '\\');
        }

        return false;
    }

    protected function normalizeClassPath($classPath)
    {
        if (
            stripos($classPath, 'interface') === false &&
            stripos($classPath, 'trait') === false
        ) {
            return 'class-' . str_replace('_', '-', strtolower($classPath));
        }

        return str_replace('_', '-', strtolower($classPath));
    }

    protected function loadMappedFile($prefix, $relativeClass)
    {
        if (isset($this->prefixes[$prefix]) === false)
            return false;

        foreach ($this->prefixes[$prefix] as $baseDir) {
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if ($this->requireFile($file))
                return $file;
        }

        return false;
    }

    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}
