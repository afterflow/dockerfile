<?php

namespace Afterflow\Dockerfile;


/**
 * Class Builder
 * @package Afterflow\Dockerfile
 */
class Builder {

    /**
     * FROM $image
     * @var string
     */
    protected $image;

    /**
     * The resulting dockerile
     *
     * @var string
     */
    protected $dockerfile = '';

    /**
     * Builder constructor.
     *
     * @param string $image
     */
    public function __construct( string $image ) {
        $this->image = $image;
        $this->line( 'FROM ' . $image );
    }

    /**
     * @param $image
     * Make a new instance
     *
     * @return static
     */
    public static function from( $image ) {
        return new static( $image );
    }

    /**
     * Compile the Dockerfile and return it as a string
     * @return string
     */
    public function compile() {
        return $this->dockerfile;
    }

    public function __toString() {
        return $this->compile();
    }

    /*
    |----------------------------------------------------------------------------
    | Builder Helpers
    |----------------------------------------------------------------------------
    */

    /**
     * @param $line
     *
     * @return $this
     */
    public function line( $line ) {
        $this->dockerfile .= $line . PHP_EOL;

        return $this;
    }

    /**
     * @param string $param
     *
     * @return $this
     */
    public function run( string $param ) {

        return $this->line( 'RUN ' . $this->jsonize( $param ) );
    }

    protected function jsonize( $param ) {

        if ( is_array( $param ) ) {
            return json_encode( $param, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        }

        return $param;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function eol( $count = 1 ) {
        for ( $i = 0; $i < $count; $i ++ ) {
            $this->line( '' );
        }

        return $this;
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function comment( $param ) {
        return $this->line( '# ' . $param );
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function blockComment( $param ) {
        $this->comment( '' );
        $this->comment( '--------------------------------------------------------------------------' );
        $this->comment( $param );
        $this->comment( '--------------------------------------------------------------------------' );
        $this->comment( '' );

        return $this;
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function arg( $param ) {
        return $this->line( 'ARG ' . $param );
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function env( $param ) {
        return $this->line( 'ENV ' . $param );
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function user( $param ) {
        return $this->line( 'USER ' . $param );
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function copy( $param ) {
        return $this->line( 'COPY ' . $param );
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function workdir( $param ) {
        return $this->line( 'WORKDIR ' . $param );
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function volume( $param ) {
        return $this->line( 'VOLUME ' . $param );
    }

    /**
     * @param $param
     *
     * @return $this
     */
    public function cmd( $param ) {
        return $this->line( 'CMD ' . $this->jsonize( $param ) );
    }

}
