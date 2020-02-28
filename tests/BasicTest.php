<?php

namespace Afterflow\Dockerfile\Tests;

use Afterflow\Dockerfile\Builder;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase {

    public function testBasicApi() {
        $dockerfile = Builder::from( 'phusion/baseimage:0.11' )->compile();
        $this->assertEquals( 'FROM phusion/baseimage:0.11' . PHP_EOL, $dockerfile );
    }

    public function testHelpers() {
        $dockerfile = Builder::from( 'phusion/baseimage:0.11' )
                             ->run( 'DEBIAN_FRONTEND=noninteractive' )
                             ->run( 'locale-gen en_US.UTF-8' )
                             ->eol()->compile();

        $this->assertStringContainsString( 'RUN DEBIAN_FRONTEND=noninteractive', $dockerfile );
    }

    public function testWorkspaceBuilder() {

        $builder    = new WorkspaceBuilder();
        $dockerfile = $builder
            ->phpVersion( '7.4' )
            ->withSoftware( [ 'findutils' ] )
            ->withExtensions( [ 'pcntl' ] )
            ->build();
        /**//**/;

        $this->assertStringContainsString( 'php7.4-pcntl', $dockerfile );
//        die ( PHP_EOL . $dockerfile );
    }


}
