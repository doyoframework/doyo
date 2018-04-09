<?php

namespace Core;

class MakerEntity
{

    /**
     * 创建Entity
     */
    public static function buildAll($db_config)
    {

        $db = Util::loadCls('Engine\MySQLi');
        $db->connect($db_config);

        $tables = $db->show_tables();

        foreach ($tables as $table) {
            $name = $table['TABLE_NAME'];

            MakerEntity::build($db_config, $name);
        }

    }

    public static function build($db_config, $name)
    {

        $db = Util::loadCls('Engine\MySQLi');
        $db->connect($db_config);

        $file = array();

        $split = strpos($name, '_');

        $prefix = substr($name, 0, $split + 1);

        $class = substr($name, $split + 1);

        $class = ucfirst(strtolower($class));

        $file[] = '<?php';
        $file[] = 'namespace Entity;';
        $file[] = '';
        $file[] = '';
        $file[] = 'use Core\BaseEntity;';
        $file[] = '';
        $file[] = 'class ' . $class . ' extends BaseEntity';
        $file[] = '{';
        $file[] = '';

        $file[] = '	var $DB_CONFIG = \'' . $db_config . '\';';
        $file[] = '	';
        $file[] = '	var $TABLE_PREFIX = \'' . $prefix . '\';';
        $file[] = '	';

        $key = $db->show_primary_key($name);

        if ($key) {
            $file[] = '	var $PRIMARY_KEY = \'' . $key . '\';';
            $file[] = '';
        }

        $fields = $db->show_fields($name, 'COLUMN_COMMENT, DATA_TYPE, COLUMN_NAME');
        foreach ($fields as $field) {

            $file[] = '	/**';
            $file[] = '	 *';
            $file[] = '	 * ' . $field['COLUMN_COMMENT'];
            $file[] = '	 *';

            $var = $field['DATA_TYPE'];

            if (in_array($var, array(
                'varchar',
                'text'
            ))) {
                $var = 'string';
            }

            $file[] = '	 * @var ' . $var;

            $file[] = '	 */';
            $file[] = '	var $' . $field['COLUMN_NAME'] . ';';
            $file[] = '	';
        }

        $file[] = '	';
        $file[] = '}';
        $file[] = '?>';

        $data = implode($file, "\n");

        $file = APP_PATH . '/Entity/' . $class . '.php';

        Util::mkdirs(dirname($file));

        if (file_exists($file)) {
            unlink($file);
        }

        file_put_contents($file, $data);

    }

}

?>