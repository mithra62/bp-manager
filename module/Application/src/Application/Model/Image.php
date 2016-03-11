<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Image.php
 */
namespace Application\Model;

/**
 * Image Model
 *
 * @package Files
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/Image.php
 *            
 */
class Image
{

    /**
     * The actual image manager
     * 
     * @var \Intervention\Image\ImageManager
     */
    private $manager = null;

    /**
     * Contains the image formats we can deal with based on system config
     * 
     * @var array
     */
    private $allowed_formats = array();

    /**
     * The permissible image formats we can use
     * 
     * @var array
     */
    private $formats = array(
        'imagick' => array(
            'tif' => array(
                'image/tiff',
                'image/x-tiff'
            ),
            'tiff' => array(
                'image/tiff',
                'image/x-tiff'
            ),
            'bmp' => array(
                'image/bmp',
                'image/x-windows-bmp'
            ),
            'bm' => array(
                'image/bmp'
            ),
            'ico' => array(
                'image/x-icon'
            ),
            'psd' => array(
                'application/octet-stream',
                'image/psd'
            )
        ),
        'gd' => array(
            'jpg' => array(
                'image/jpeg',
                'image/pjpeg'
            ),
            'jpeg' => array(
                'image/jpeg',
                'image/pjpeg'
            ),
            'png' => array(
                'image/png'
            ),
            'x-png' => array(
                'image/png'
            ),
            'gif' => array(
                'image/gif'
            )
        )
    );

    /**
     *
     * @ignore
     *
     * @param \Intervention\Image\ImageManager $image_manager            
     */
    public function __construct(\Intervention\Image\ImageManager $image_manager = null)
    {
        $this->manager = $image_manager;
        $this->formats['imagick'] = array_merge($this->formats['imagick'], $this->formats['gd']);
        $this->allowed_formats = $this->formats[$this->manager->config['driver']];
    }

    /**
     * Handles the processing of the images
     * 
     * @param string $name            
     * @param string $path            
     * @return bool
     */
    public function processImage($name, $path, $image_data = false)
    {
        if (! $this->canProcess($path . DS . $name)) {
            return false;
        }
        
        if (! $image_data) {
            $image_data = getimagesize($path . DS . $name);
        }
        
        if ('image/psd' == $image_data['mime']) {
            $name = $this->convertPsd($path, $name, 'jpg');
            if (! $name) {
                return false; // we couldn't work with the PSD do no need to proceed
            }
        }
        
        $this->resize($path . DS . $name, $path . DS . 'mid_' . $name, 700, false);
        $this->resize($path . DS . $name, $path . DS . 'tb_' . $name, 100, false);
        return true;
    }

    /**
     * Verifies we can actually do magic against the file
     * 
     * @param string $file            
     * @return boolean
     */
    public function canProcess($file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (! empty($this->allowed_formats[$ext]) && is_array($this->allowed_formats[$ext])) {
            $details = $this->getSize($file);
            $mime = image_type_to_mime_type($details['type']);
            if (in_array($mime, $this->allowed_formats[$ext])) {
                return true;
            }
        }
    }

    /**
     * Determines what the preview size should be
     * 
     * @param string $view_size            
     * @return string
     */
    public function getPreviewSize($view_size)
    {
        switch ($view_size) {
            case 'mid':
            case 'tb':
                $view_size = $view_size . '_';
                break;
            
            default:
                $view_size = 'mid_';
                break;
        }
        return $view_size;
    }

    /**
     * Takes a file and returns the format / type
     *
     * @param string $image_name
     *            to the file
     * @global array $vars System settings; used for ImageMagickPath
     * @return string
     */
    public function identify($image_name)
    {
        $exec_str = $this->convert . " identify -verbose $image_name | grep 'Geometry\|Resolution\|Format\|Filesize'";
        $image_resize_exe = exec($exec_str, $output);
        return $output;
    }

    /**
     * Resizes an image to specified height and width
     *
     * @param string $image_name
     *            to orignal file
     * @param string $dest_name
     *            to saved file
     * @param int $t_width
     *            the new image
     * @param int $t_height
     *            new image
     * @return void
     */
    public function resize($image_name, $dest_name, $t_width = 0, $t_height = 0)
    {
        $stats = $this->getSize($image_name);
        if ($stats['height'] < $t_height && $stats['width'] < $t_width) {
            copy($image_name, $dest_name);
            return;
        }
        
        $image = $this->manager->make($image_name)->resize($t_width, $t_height, function ($constraint) {
            $constraint->aspectRatio();
        });
        
        $image->save($dest_name);
    }

    /**
     * Resizes a psd to specified height and width
     *
     * @param string $image_name
     *            to orignal file
     * @param string $type
     *            to saved file
     * @return void
     */
    public function convertPsd($file_path, $file_name, $type)
    {
        $new_name = str_replace('.psd', '.' . str_replace('.', '', $type), $file_name);
        $this->manager->make($file_path . '/' . $file_name)
            ->encode($type)
            ->save($file_path . '/' . $new_name);
        return $new_name;
    }

    /**
     * Returns an array with the image data
     * 
     * @param string $image            
     * @return array
     */
    public function getSize($image)
    {
        $s = array();
        if (file_exists($image)) {
            list ($s['width'], $s['height'], $s['type'], $s['attr']) = getimagesize($image);
        }
        
        return $s;
    }
}