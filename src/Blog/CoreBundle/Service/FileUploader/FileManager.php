<?php

namespace Blog\CoreBundle\Service\FileUploader;

class FileManager extends \PunkAve\FileUploaderBundle\Services\FileManager
{
    private function normalizePath($path)
    {
        $parts = array();// Array to build a new path from the good parts
        $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
        $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
        $segments = explode('/', $path);// Collect path segments
        $test = '';// Initialize testing variable
        foreach($segments as $segment)
        {
            if($segment != '.')
            {
                $test = array_pop($parts);
                if(is_null($test))
                    $parts[] = $segment;
                else if($segment == '..')
                {
                    if($test == '..')
                        $parts[] = $test;

                    if($test == '..' || $test == '')
                        $parts[] = $segment;
                }
                else
                {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }
        return implode('/', $parts);
    }

    /**
     * Sync existing files from one folder to another. The 'fromFolder' and 'toFolder'
     * options are required. As with the 'folder' option elsewhere, these are appended
     * to the file_base_path for you, missing parent folders are created, etc. If 
     * 'fromFolder' does not exist no error is reported as this is common if no files
     * have been uploaded. If there are files and the sync reports errors an exception
     * is thrown.
     * 
     * If you pass consistent options to this method and handleFileUpload with
     * regard to paths, then you will get consistent results.
     */
    public function syncFiles($options = array())
    {
        $options = array_merge($this->options, $options);

        // We're syncing and potentially deleting folders, so make sure
        // we were passed something - make it a little harder to accidentally
        // trash your site
        if (!strlen(trim($options['file_base_path'])))
        {
            throw \Exception("file_base_path option looks empty, bailing out");
        }
        if (!strlen(trim($options['from_folder'])))
        {
            throw \Exception("from_folder option looks empty, bailing out");
        }
        if (!strlen(trim($options['to_folder'])))
        {
            throw \Exception("to_folder option looks empty, bailing out");
        }

        $from = $this->normalizePath($options['file_base_path'] . '/' . $options['from_folder']);
        $to = $this->normalizePath($options['file_base_path'] . '/' . $options['to_folder']);
        if (file_exists($from))
        {
            if (isset($options['create_to_folder']) && $options['create_to_folder'])
            {
                @mkdir($to, 0777, true);
            }
            elseif (!file_exists($to))
            {
                throw new \Exception("to_folder does not exist");
            }
            $result = null;
            system("rsync -a " . escapeshellarg($from . '/') . " " . escapeshellarg($to), $result);
            if ($result !== 0)
            {
                throw new \Exception("Sync failed");
            }
            if (isset($options['remove_from_folder']) && $options['remove_from_folder'])
            {
                system("rm -rf " . escapeshellarg($from));
            }
        }
        else
        {
            // A missing from_folder is not an error. This is commonly the case
            // when syncing from something that has nothing attached to it yet, etc.
        }
    }

    /**
     * Get a list of files already present. The 'folder' option is required.
     * If you pass consistent options to this method and handleFileUpload with
     * regard to paths, then you will get consistent results.
     */
    public function getFiles($options = array())
    {
        $options = array_merge($this->options, $options);

        $folder = $this->normalizePath($options['file_base_path'] . '/' . $options['folder']);
        if (file_exists($folder))
        {
            // Carlos. 14/07/15. Add subfolder options to look for files from
            $dirs = glob("$folder/originals/*");
            if (isset($options['subfolder'])) {
                $dirs = glob("$folder/{$options['subfolder']}/*");
            }
            $fullPath = isset($options['full_path']) ? $options['full_path'] : false;
            if ($fullPath)
            {
                return $dirs;
            }
            if (!is_array($dirs)) {
                $dirs = array();
            }
            $result = array_map(function($s) { return preg_replace('|^.+[\\/]|', '', $s); }, $dirs);
            return $result;
        }
        else
        {
            return array();
        }
    }

    /**
     * Remove the folder specified by 'folder' and its contents.
     * If you pass consistent options to this method and handleFileUpload with
     * regard to paths, then you will get consistent results.
     */
    public function removeFiles($options = array())
    {
        $options = array_merge($this->options, $options);

        $folder = $this->normalizePath($options['file_base_path'] . '/' . $options['folder']);

        if (!strlen(trim($options['file_base_path'])))
        {
            throw \Exception("file_base_path option looks empty, bailing out");
        }

        if (!strlen(trim($options['folder'])))
        {
            throw \Exception("folder option looks empty, bailing out");
        }
        system("rm -rf " . escapeshellarg($folder));
    }
}