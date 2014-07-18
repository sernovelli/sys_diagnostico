<?php
/**
 * 
 * Responsavel por fazer o upload de relatorios e fotos
 * @author Rafael Wendel Pinheiro
 * @version 1.0
 * @link https://github.com/rafaelwendel/UploadHelper
 * 
 * @adaptedby Sergio Novelli
 * @version 1.1
 *
 */
class UploadHelper {
    
    /** The file to be sent */
    protected $_file;
    
    /** The extension of file */
    protected $_ext;
    
    /** The folder to receive the file */
    protected $_uploads_folder;
    
    /** The uploaded file path */
    protected $_file_path;
    
    /** The new name of file */
    protected $_file_name = null;

    /** The allowed extensions in the upload */
    protected $_allowed_exts = array();

    /** The max size of file (in MegaBytes) */
    protected $_max_size = 2;
    
    /** Overwrite file with same name? */
    protected $_overwrite = true;
    
    /** The error message */
    protected $_error;
    
    /** The default messages */
    protected $_default_messages = array();
    
    /** The language of messages. (English (en) or Portuguese (pt) */
    protected $_language = 'pt';
    
    
    /**
     * Constructor method. You can define the file, uploads folder, new file name and the language. Define too the default messages
     */
    public function __construct($file = '', $uploads_folder = '', $file_name = '', $language = 'pt') {
        if (isset ($file)){
            $this->set_file($file);
        }        
        
        if (isset ($uploads_folder)){
            $this->set_uploads_folder($uploads_folder);
        }        
        
        if (isset ($file_name)){
            $this->set_file_name($file_name);
        }
        
        $this->set_language($language);
        $this->set_default_messages();
    }
    
    
    /**
     * Set the default messages
     */
    protected function set_default_messages(){
        $this->_default_messages['en'] = array(
            '1' => 'File is not set',
            '2' => 'Uploads folder is not set or no exists',
            '3' => 'Files of type {{exts}} are not allowed',
            '4' => 'The file size is larger than {{max_size}}MB',
            '5' => 'Error when uploading'
        );
        
        $this->_default_messages['pt'] = array(
            '1' => 'Arquivo não setado',
            '2' => 'Pasta de uploads não definida ou não existe',
            '3' => 'Arquivos do tipo {{exts}} não são permitidos',
            '4' => 'O tamanho do arquivo é maior que {{max_size}}MB',
            '5' => 'Erro ao fazer upload'
        );
    }
    
    /**
     * Set the language messages
     */
    public function set_language($language){
        $this->_language = ($language == 'en' || $language == 'pt' ? $language : 'en');
    }
    
    /**
     * Set a file
     */
    public function set_file($file){
        $this->_file = $file;
        $this->set_ext($file);
    }
    
    /**
     * Set the extension of file
     */
    protected function set_ext($file){
        $this->_ext = strtolower(end(explode('.', $file['name'])));
    }
    
    /**
     * Set the folder to receive the file
     */
    public function set_uploads_folder($uploads_folder){
        if (substr ($uploads_folder, -1) == '/') {
            $this->_uploads_folder = $uploads_folder;
        }
        else{
            $this->_uploads_folder = $uploads_folder . '/';
        }
    }
    
    /**
     * Set the new name of the file
     */
    public function set_file_name($file_name){
        $this->_file_name = $file_name;
    }
    
    /**
     * Set the max size of file
     */
    public function set_max_size($max_size){
        $this->_max_size = $max_size;
    }
    
    /**
     * Set the allowed extensions in the upload
     */
    public function set_allowed_exts($allowed_exts){
        if (is_array ($allowed_exts)){
            $this->_allowed_exts = $allowed_exts;
        }
        if (is_string($allowed_exts)){
            $this->_allowed_exts[] = $allowed_exts;
        }
    }
    
    /**
     * Overwrite file with same name? (true or false)
     */
    public function set_overwrite($param){
        $this->_overwrite = (bool) $param;
    }

    /**
     * Set a error message
     */
    protected function set_error($error_num){
        $this->_error = $this->_default_messages[$this->_language][$error_num];
    }

    /**
     * Get the error message
     */
    public function get_error(){
        $this->_error = str_replace('{{exts}}', $this->_ext, $this->_error);
        $this->_error = str_replace('{{max_size}}', $this->_max_size, $this->_error);
        
        return $this->_error;
    }

    /**
     * Get the uploaded file path
     */
    public function get_file_path(){
        return $this->_file_path;
    }    
    
    /**
     * Keep the file with the same name
     */
    protected function some_name(){
        $tmp_name = explode('.', $this->_file['name']);        
        unset($tmp_name[count($tmp_name) - 1]);
        
        $this->_file_name = implode('.', $tmp_name);
    }

    /**
     * Checks whether to overwrite files with the same name. If not, creates name incremented ($name, $name_1, $name_2, $name_n)
     */
    protected function verify_overwrite() {
        if (!$this->_overwrite){
            $tmp_name = $this->_file_name;
            $x = 1;
            
            while (file_exists($this->_uploads_folder . $this->_file_name . '.' . $this->_ext)) {
                $this->_file_name = $tmp_name . '_' . $x;
                $x++;
            }
        }
    }

    /**
     * Validates requirements for uploading
     */
    protected function is_valid(){
        if (empty ($this->_file['name'])) {
            $this->set_error(1);
            return false;
        }
        if (empty ($this->_uploads_folder) || !file_exists($this->_uploads_folder)){
            $this->set_error(2);
            return false;
        }
        if (!in_array ($this->_ext, $this->_allowed_exts)){
            $this->set_error(3);
            return false;
        }
        if (!$this->validate_size()) {
            $this->set_error(4);
            return false;
        }
        return true;
    }

    /**
     * Validate the size of file
     */
    protected function validate_size(){
        $file_size = $this->_file['size'];
        
        /* Convert bytes to megabytes */
        $file_size = ($file_size / 1024) / 1024;
        
        if ($file_size > $this->_max_size) {
            return false;
        }
        
        return true;
    }

    /**
     * Upload the file
     */
    public function upload_file() {
	   if (!$this->is_valid()) {
            return false;
        }
        
        if (!isset ($this->_file_name)) {
            $this->some_name();
        }
        
        $this->verify_overwrite();
        
        if (move_uploaded_file ($this->_file['tmp_name'], $this->_uploads_folder . $this->_file_name . '.' . $this->_ext)){
            $this->_file_path = $this->_uploads_folder . $this->_file_name . '.' . $this->_ext;
            return true;
        }
        else {
            $this->set_error(5);
            return false;
        }
    }    
}