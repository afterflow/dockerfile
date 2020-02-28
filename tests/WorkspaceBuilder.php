<?php


namespace Afterflow\Dockerfile\Tests;


use Afterflow\Dockerfile\Builder;

class WorkspaceBuilder {

    protected $locale = 'en_US.UTF-8';
    /**
     * @var Builder
     */
    protected $builder;

    protected $phpVersion = '7.3';

    protected $software = [
        'pkg-config',
        'libcurl4-openssl-dev',
        'libedit-dev',
        'libssl-dev',
        'libxml2-dev',
        'xz-utils',
        'libsqlite3-dev',
        'sqlite3',
        'git',
        'curl',
        'vim',
        'nano',
        'postgresql-client',
    ];

    protected $extensions = [
        // Not exactly extensions but we need them
        'cli',
        'common',
        'dev',
        //
        'curl',
        'intl',
        'pcntl',
        'json',
        'xml',
        'mbstring',
        'mysql',
        'pgsql',
        'sqlite',
        'sqlite3',
        'zip',
        'bcmath',
        'memcached',
        'gd',
    ];

    public function __construct() {
        $this->builder = Builder::from( 'phusion/baseimage:0.11' );
    }

    public function phpVersion( $version ) {
        $this->phpVersion = $version;

        return $this;
    }

    public function withExtensions( $packages ) {
        $this->extensions = array_merge( $this->extensions, $packages );

        return $this;
    }

    public function withoutExtensions( $packages ) {
        $this->extensions = array_diff( $this->extensions, $packages );

        return $this;
    }

    public function withSoftware( $packages ) {
        $this->software = array_merge( $this->software, $packages );

        return $this;
    }

    public function withoutSoftware( $packages ) {
        $this->software = array_diff( $this->software, $packages );

        return $this;
    }

    public function build() {

        $this->builder->run( 'DEBIAN_FRONTEND=noninteractive' )
                      ->eol()
                      ->run( 'locale-gen ' . $this->locale )
                      ->env( 'LANGUAGE=' . $this->locale )
                      ->env( 'LC_ALL=' . $this->locale )
                      ->env( 'LC_CTYPE=' . $this->locale )
                      ->env( 'LANG=' . $this->locale )
                      ->eol()
                      ->env( 'TERM=xterm' )
                      ->eol()
                      ->comment( 'Add PHP PPA' )
                      ->run( 'apt-get install -y software-properties-common && add-apt-repository -y ppa:ondrej/php' )
                      ->run( 'echo \'DPkg::options { "--force-confdef"; };\' >> /etc/apt/apt.conf' )->eol( 2 )
            /**//**/
        ;

        $this->builder->blockComment( 'Install Software' )->eol()
                      ->run( 'apt-get update && apt-get upgrade -y && apt-get install -y --allow-downgrades --allow-remove-essential --allow-change-held-packages \ '
                             . PHP_EOL . '    '
                             . implode( ' \ ' . PHP_EOL . '    ', $this->getSoftware() )
                             . ' \ ' . PHP_EOL
                             . '    && apt-get-clean' );

        $this->builder->eol();


        return $this->builder->compile();
    }


    /**
     * @param string $locale
     *
     * @return WorkspaceBuilder
     */
    public function locale( $locale ) {
        $this->locale = $locale;

        return $this;
    }

    protected function extensionsAsPackages() {
        $ret = [];
        foreach ( array_unique( $this->extensions ) as $x ) {
            $ret [] = 'php' . $this->phpVersion . '-' . $x;
        }

        return $ret;
    }

    protected function getSoftware() {

        return array_unique( array_merge( $this->software, $this->extensionsAsPackages() ) );

    }

}
