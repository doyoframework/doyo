<?php

namespace Core;

class MakerModel
{

    public static function buildForm($db_config)
    {

        $db = Util::loadCls('Engine\MySQLi');
        $db->connect($db_config);

        $tables = $db->show_tables('TABLE_NAME, TABLE_COMMENT');

        $model = array();
        foreach ($tables as $table) {
            $name = $table['TABLE_NAME'];

            $fields = $db->show_fields($name, 'COLUMN_COMMENT, DATA_TYPE, COLUMN_DEFAULT, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, COLUMN_NAME');

            echo "<hr/>";
            echo $name;
            echo "<hr/>";

            foreach ($fields as $field) {

                if ($field['DATA_TYPE'] == 'text' || $field['DATA_TYPE'] == 'varchar') {
                    $limit = $field['CHARACTER_MAXIMUM_LENGTH'];
                } else {
                    $limit = $field['NUMERIC_PRECISION'];
                }

                if ($field['DATA_TYPE'] == 'text' || $field['DATA_TYPE'] == 'varchar') {
                    $type = "text";
                } else {
                    $type = "number";
                }

                // echo '$form[] = array("name"=>"'. $field['COLUMN_NAME'].'", "type"=>"'.$type.'", "limit"=>"1-'.$limit.'", "value"=>"'.$field['COLUMN_DEFAULT'].'", "label"=>"'.$field['COLUMN_COMMENT'].'");';
                echo '"' . $field['COLUMN_NAME'] . '" => array("type"=>"' . $type . '", "limit"=>"1-' . $limit . '", "value"=>"' . $field['COLUMN_DEFAULT'] . '", "label"=>"' . $field['COLUMN_COMMENT'] . '"),';
                // $model[$field['COLUMN_NAME']] = 'array("type"=>"'.$type.'", "limit"=>"1-'.$limit.'", "value"=>"'.$field['COLUMN_DEFAULT'].'", "label"=>"'.$field['COLUMN_COMMENT'].'"),';
                echo "<br>";
            }
        }

        foreach ($model as $k => $v) {
            echo '"' . $k . '" => ' . $v;
            echo '<br/>';
        }

    }

    /**
     * 创建Model
     *
     * @param $db_config
     */
    public static function buildAll($db_config)
    {

        $db = Util::loadCls('Engine\MySQLi');
        $db->connect($db_config);

        $tables = $db->show_tables('TABLE_NAME, TABLE_COMMENT');

        $model = array();

        $model[] = '<?php';
        $model[] = 'namespace Common;';
        $model[] = '';
        $model[] = 'use Core\Util;';
        $model[] = '';
        $model[] = 'class Model extends IModel';
        $model[] = '{';
        $model[] = '';

        foreach ($tables as $table) {
            $name = $table['TABLE_NAME'];

            $split = strpos($name, '_');
            
            $class = substr($name, $split + 1);

            $class = ucfirst(strtolower($class));

            $class = ucfirst(strtolower($class));

            $methodName = implode('', array_map('ucfirst', explode('_', $class)));

            $key = $db->show_primary_key($name);

            if (empty($key)) {
                $key = 'id';
            }

            $model[] = '	/**';
            $model[] = '	 *';
            $model[] = '	 * ' . $class . $table['TABLE_COMMENT'];
            $model[] = '	 *';
            $model[] = '	 * @param int $' . $key;
            $model[] = '	 *';
            $model[] = '	 * @return \Entity\\' . $class . '|\\Model\\' . $class;
            $model[] = '	 *';
            $model[] = '	 */';
            $model[] = '	public static function ' . $methodName . '($' . $key . ' = 0)';
            $model[] = '	{';
            $model[] = '	';
            $model[] = '		return Util::loadModel(\'Model\\' . $class . '\', \'' . $class . '\', $' . $key . ');';
            $model[] = '	';
            $model[] = '	}';
            $model[] = '	';

            $file = array();
            $file[] = '<?php';
            $file[] = 'namespace Model;';
            $file[] = '';
            $file[] = 'use Core\BaseModel;';
            $file[] = '';
            $file[] = 'class ' . $class . ' extends BaseModel';
            $file[] = '{';
            $file[] = '	';
            $file[] = '	';
            $file[] = '}';
            $file[] = '?>';

            $data = implode($file, "\n");

            $file = APP_PATH . '/Model/' . $class . '.php';

            Util::mkdirs(dirname($file));

            if (!file_exists($file)) {
                file_put_contents($file, $data);
            }
        }

        $model[] = '	';
        $model[] = '}';
        $model[] = '?>';

        $data = implode($model, "\n");

        $file = APP_PATH . '/Common/Model.php';

        if (file_exists($file)) {
            unlink($file);
        }

        file_put_contents($file, $data);

    }

}