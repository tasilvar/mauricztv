<?php
namespace bpmj\wpidea\admin\tools\importer;

class File_Parser{    
    /**
     * Returns file content as an array
     *
     * @param string $file url
     * @return array
     */
    public static function get_array_from_csv( $file_url )
    {
        $array = array_map('str_getcsv', file( $file_url ));

        return $array;        
    }

    /**
     * Get file size in bytes
     *
     * @param string $fileUrl
     * @return int
     */
    public static function get_file_size_in_bytes( $fileUrl )
    {         
        $headers = get_headers($fileUrl, 1);
         
        //Convert the array keys to lower case for the sake of consistency.
        $headers = array_change_key_case($headers);

        return isset( $headers[ 'content-length' ] ) ? $headers[ 'content-length' ] : 0;
    }
}