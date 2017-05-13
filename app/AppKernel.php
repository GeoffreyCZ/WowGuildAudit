<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    private $gcsBucketName;

    public function __construct($environment = null, $debug = null)
    {
        // determine the environment / debug configuration based on whether or not this is running
        // in App Engine's Dev App Server, or in production
        if (is_null($debug)) {
            $debug = !Environment::onAppEngine();
        }

        if (is_null($environment)) {
            $environment = $debug ? 'dev' : 'prod';
        }

        parent::__construct($environment, $debug);

        // Symfony console requires timezone to be set manually.
        if (!ini_get('date.timezone')) {
          date_default_timezone_set('UTC');
        }

        // Enable optimistic caching for GCS.
        $options = ['gs' => ['enable_optimsitic_cache' => true]];
        stream_context_set_default($options);

        $this->gcsBucketName = getenv('GCS_BUCKET_NAME');
    }

    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new WowGuildAudit\WowGuildAudit(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        if ($this->gcsBucketName) {
            return sprintf('gs://%s/symfony/cache%s', $this->gcsBucketName, $this->getVersionSuffix());
        }

        return parent::getCacheDir();
    }

    public function getLogDir()
    {
        if ($this->gcsBucketName) {
            return sprintf('gs://%s/symfony/log', $this->gcsBucketName);
        }

        return parent::getLogDir();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    private function getVersionSuffix()
    {
        $version = getenv('CURRENT_VERSION_ID');

        // CURRENT_VERSION_ID in PHP represents major and minor version
        if (1 === substr_count($version, '.')) {
            list($major, $minor) = explode('.', $version);

            return '-' . $major;
        }
    }
}
