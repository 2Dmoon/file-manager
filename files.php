<?php
    namespace file;

    /**
     * Author: Jackie Moon
     * Date: 20.01.2019
     * Description: Class for file manager on PHP language
     */

    class Files {

        public $formats = ['mp4', 'ogg', 'docx', 'doc', 'pdf', 'jpeg', 'jpg', 'png', 'xls', 'xlsx'];
        public $path;

        /**
         * construct
         *
         * @param [string] $path
         */
        function __construct($path) {

           $this->path = $path;

        }
        

        /**
         * set - функция добавления файлов
         * 
         * @param [$_FILES] $files
         * @param [string] $url
         * @return void
         */
        public function set ($files, $url){

            if (!isset($files)) return ["status" => false, "message" => "Файлов для загрузки обнаружено!"];
            foreach ($files as $key => $value) {	   
                    $mk = microtime(true);
                    $prename = explode(".", $value['name']);
                    $name = end($prename);
                    if (!in_array($name, $formats)){ 
                        $message =  "Ошибка, пристутствует файл с недопустимым форматом! Список разрешенных форматов 'mp4', .ogg', 'docx', 'doc', 'pdf', 'jpeg', 'jpg', 'png', 'xls', 'xlsx'";
                        continue;
                    }
                    $path = (isset($url))? $url.$mk.'.'.$name : $mk.'.'.$name;
                    if(!move_uploaded_file($value['tmp_name'], $this->path.$path))
                        return ["status" => false, "message" => "Ошибка при загрузке файла!"];
                    $names[] = $path;
            }
            if (isset($message)) $names['message'] = $message;
            return $names;

        }

        /**
         * get - filelist of url
         *
         * @param [string] $url
         * @return void
         */
        public function get ($url){

            $path = (isset($url))?$this->path.$url:$this->path;
            $list = scandir($path);
            $list = array_slice($list, 2);

            if (!$list) return ["status"=> false, "message"=> "В этом каталоге нет файлов"];
            
            foreach ($list as $key => $value) {
                $prename = explode(".", $value);
                $name = (is_dir($path.$value))?"dir":end($prename);
    
                $result[] = [
                    'path' => (isset($_POST['url']))?$_POST['url'].$value:$value,
                    'name' => $value,
                    'format' => $name,
                    'size' => filesize($path.$value)
                ];
            }
            
            return $result;

        }

        /**
         * delete - delete file
         *
         * @param [type] $url
         * @return void
         */
        public function delete ($url){

            if (!isset($url)) 
                
                return ["status"=>false, "message"=>"Должен присутствовать url"];

            return (deletePath($this->path.$url))
                ?["status"=>true, "message"=>"Удаление прошло успешно!"]
                :["status"=>false, "message"=>"Не получилось удалить!"];
    
        }

        /**
         * deletePath - delete folder by URL 
         *
         * @param [url] $url
         * @return void
         */
        private function deletePath ($url){

            if (is_dir($url) === true){
                $files = array_diff(scandir($url), array('.', '..'));
                foreach ($files as $file){
                    deletePath(realpath($url) . '/' . $file);
                }
                return rmdir($url);
            }else if (is_file($url) === true){
                return unlink($url);
            }
            return false;

        }

        /**
         * addDir - add folder in in the $url
         *
         * @param [string] $url
         * @return void
         */
        function addDir ($url){

            if (!isset($url)) return ["status"=>false, "message"=>"Должен присутствовать url"];
            $path = $this->path.$url;
            return (mkdir($path, 0777))
                ?["status"=>true, "message"=>"Успех"]
                :["status"=>false, "message"=>"Не удалось создать директорию"];
    
        }

    }
