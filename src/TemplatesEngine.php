<?php

namespace src;

#[\Attribute]
class TemplatesEngine {

    private string $templatesPath = '';
    private array $templates = array();
    public string $toRender = '';

    public function __construct( string $path ) {
        $this->toRender = $path;
    }

    /**
     * Set template engine main directory to scan
     *
     * @param string $directory
     * @return TemplatesEngine
     */
    public function setDirectory( string $directory ) : TemplatesEngine {
        $this->templatesPath = $directory;
        return $this;
    }

    /**
     * Scan all templates from the templatePath directory (/templates)
     *
     * @param string|null $directory
     * @return TemplatesEngine
     */
    public function scanTemplates( string $directory=null ) : TemplatesEngine {
        if( $directory === null ) $directory = $this->templatesPath;
        if(
            empty( $directory ) ||
            ! is_dir( $directory )
        ) return $this;
        $files = array_map(
            fn( $x ) => "$directory/$x",
            array_diff( scandir( $directory ), array( '.', '..' ) )
        );
        foreach( $files as $file ) {
            if( is_dir( $file ) ) {
                $this->scanTemplates( $file );
            } else if( file_exists( $file ) && is_file( $file ) ) {
                if( dirname( $file ) === $this->templatesPath ) {
                    $this->templates[ pathinfo( $file, PATHINFO_FILENAME ) ] = $file;
                } else {
                    $dirname = str_replace(
                        '/',
                        '-',
                        trim( str_replace( $this->templatesPath, '', dirname( $file ) ), '/' )
                    );
                    $this->templates[ "$dirname-" . pathinfo( $file, PATHINFO_FILENAME ) ] = $file;
                }
            }
        }
        return $this;
    }

    /**
     * Page when no templates implemented yet
     *
     * @return void
     */
    private function noTemplates() : void {
        echo "No templates implemented yet. Create one to continue.";
    }

    /**
     * Load template from path name if exists
     */
    public function loadTemplate( string $path ) : void {
        if( in_array( $path, array_keys( $this->templates ) ) ) {
            require_once $this->templates[ $path ];
        } else {
            $this->noTemplates();
        }
    }

}