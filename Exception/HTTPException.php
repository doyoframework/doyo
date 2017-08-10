<?php
namespace Exception;

class HTTPException extends \Exception {

    /**
     * 扩展信息
     *
     * @var array
     */
    private $data = array ();

    public function __construct($message, $data = array()) {

        if (!is_array($data)) {
            $data = array (
                'errMsg' => $data 
            );
        }
        
        $this->data = $data;
        
        parent::__construct($message);
    
    }

    /**
     * 获取扩展信息
     *
     * @return array
     */
    public function getData() {

        return $this->data;
    
    }

}
?>