<?php

namespace vendors\BeFeW;

class Entity {
    private $befewLinks = array();
    private $befewAttributes = array(
        'id' => array(
            'type' => 'int',
            'autoIncrement' => true,
            'index' => 'primary'
        )
    );
    private $befewDefaultAttributes = array(
        'type'          => 'varchar',
        'default'       => null,
        'collation'     => 'utf8_general_ci',
        'attributes'    => null,
        'null'          => false,
        'index'         => null,
        'autoIncrement' => false,
        'comments'      => null
    );
    private $befewTableCollation = 'utf8_general_ci';

    protected $id;

    public function __construct($id = null) {
        if($id != null) {
            $this->find($id);
        }
    }

    public function __call($method, $args) {
        if(substr($method, 0, 3) == "get") {
            return $this->get(lcfirst(str_replace('get', '', $method)));
        } else if (substr($method, 0, 3) == "set") {
            return $this->set(lcfirst(str_replace('set', '', $method)), $args[0]);
        } else {
            throw new \Exception('Unknown method "' . $method . '" called');
        }
    }

    protected function get($element) {
        return $this->{$element};
    }

    protected function set($element, $value) {
        return $this->{$element} = $value;
    }

    protected function getTableName() {
        return strtolower(substr(get_called_class(), strrpos(get_called_class(), '\\') + 1));
    }

    protected function setTableCollation($collation) {
        $this->befewTableCollation = $collation;
    }

    protected function getTableCollation() {
        return $this->befewTableCollation;
    }

    protected function registerAttribute($name, $settings) {
        $this->befewAttributes[$name] = $settings;
    }

    protected function registerAttributes($set) {
        foreach($set as $name => $settings) {
            $this->registerAttribute($name, $settings);
        }
    }

    protected function registerLink($link) {
        $this->befewLinks[] = $link;
    }

    protected function registerLinks($set) {
        $this->befewLinks = array_merge($this->befewLinks[], $set);
    }

    public function find($id) {
        global $DBH;

        $query = $DBH->prepare('SELECT * FROM ' . $this->getTableName() . ' WHERE id = :id');

        if($query) {
            $query->execute(array(':id' => $id));
            $datas = $query->fetch();

            foreach ($datas as $key => $value) {
                if (!is_int($key)) {
                    if(substr($key, 0, 3) == 'id_') {
                        $child = new \ReflectionObject($this);
                        $key = substr($key, 3);
                        $objectName = $child->getNamespaceName() . '\\' . ucfirst($key);
                        return $this->{$key} = new $objectName($value);
                    } else {
                        $this->{$key} = $value;
                    }
                }
            }

            return true;
        }

        return false;
    }

    public function save() {
        global $DBH;

        if($this->id != null) {
            $q = 'UPDATE ' . $this->getTableName() . ' SET ';
            $i = 0;

            foreach ($this as $key => $value) {
                if(substr($key, 0, 5) != 'befew') {
                    if($i > 0) {
                        $q .= ', ';
                    }
                    if(in_array($key, $this->befewLinks)) {
                        $q .= 'id_' . $key . ' = :' . $key;
                    } else {
                        $q .= $key . ' = :' . $key;
                    }

                    $i++;
                }
            }

            $q .= ' WHERE id = ' . intval($this->id);
        } else {
            $i = 0;
            $q = 'INSERT INTO ' . $this->getTableName() . ' (';
            foreach ($this as $key => $value) {
                if(substr($key, 0, 5) != 'befew') {
                    if($i > 0) {
                        $q .= ', ';
                    }
                    if(in_array($key, $this->befewLinks)) {
                        $q .= 'id_' . $key;
                    } else {
                        $q .= $key;
                    }

                    $i++;
                }
            }

            $i = 0;
            $q .= ') VALUES(';
            foreach ($this as $key => $value) {
                if(substr($key, 0, 5) != 'befew') {
                    if($i > 0) {
                        $q .= ', ';
                    }
                    $q .= ':' . $key;
                }

                $i++;
            }

            $q .= ')';
        }

        $values = array();

        foreach ($this as $key => $value) {
            if(substr($key, 0, 5) != 'befew') {
                if(in_array($key, $this->befewLinks)) {
                    $values[':' . $key] = $value->getId();
                } else {
                    $values[':' . $key] = $value;
                }
            }
        }

        $query = $DBH->prepare($q);

        try {
            $query->execute($values);
            return true;
        } catch (\PDOException $e) {
            if(DEBUG) {
                Logger::error($e->errorInfo[2]);
                Logger::error('For more informations, you can take a look at the query : ');
                Logger::error(Utils::getQueryWithValues($q, $values));
            }
            return false;
        }
    }

    public function drop() {
        global $DBH;

        return (bool) $DBH->query('DROP TABLE ' . $this->getTableName());
    }

    public function uninstall() {
        $child = new \ReflectionObject($this);

        $this->drop();
        unlink($child->getFileName());
    }

    public function delete() {
        if($this->id != null) {
            global $DBH;

            return (bool) $DBH->query('DELETE FROM ' . $this->getTableName() . ' WHERE id=' . $this->id);
        } else {
            return true;
        }
    }

    public function isTableCreated() {
        global $DBH;

        $results = $DBH->query('SHOW TABLES LIKE "' . $this->getTableName() . '"');

        if(!$results) {
            Logger::error(print_r($dbh->errorInfo(), true));
        } if($results->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }

    public function tableMatches($repair = false) {
        $errors = array();
        $index = 0;

        if(!$this->isTableCreated()) {
            $index = (isset($errors[$index])) ? $index + 1 : $index;
            $errors[$index]['query'] = $this->createTable(true);

            if($repair) {
                if(!$this->createTable()) {
                    $errors[$index]['error'] = 'Process error: table `' . $this->getTableName() . '` could not be created';
                }
            } else {
                $errors[$index]['error'] = 'Database error: table `' . $this->getTableName() . '` doesn\'t exists';
                $errors[$index]['fix'] = 'Create the table `' . $this->getTableName() . '`';
            }
        } else {
            global $DBH;
            $query = $DBH->query('DESCRIBE ' . $this->getTableName());
            $datas = $query->fetchAll();
            $fields = array();

            for($i = 0; $i < count($datas); $i++) {
                $field = $datas[$i];
                $fieldNameInClass = (substr($field['Field'], 0, 3) == 'id_') ? substr($field['Field'], 3) : $field['Field'];
                $fields[] = $fieldNameInClass;

                if(!property_exists($this, $fieldNameInClass)) {
                    $index = (isset($errors[$index])) ? $index + 1 : $index;
                    $errors[$index]['query'] = 'ALTER TABLE `' . $this->getTableName() . '` DROP `' . $field['Field'] . '`';

                    if($repair) {
                        if(!$DBH->exec($errors[$index]['query'])) {
                            $errors[$index]['error'] = 'Process error: field `' . $field['Field'] . '` could not be deleted';
                        }
                    } else {
                        $errors[$index]['error'] = 'Database error: field `' . $field['Field'] . '` was found in table `' . $this->getTableName() . '` but doesn\'t exist in class `' . get_called_class() . '`';
                    }
                } else {
                    $structure = $this->getFieldStructure($field['Field']);

                    if($field['Type'] != $structure['type'] . $structure['length']) {
                        $index = (isset($errors[$index])) ? $index + 1 : $index;
                        $errors[$index]['query'] = $this->getFieldStructure($fieldNameInClass, true);

                        if($repair) {
                            if(!$DBH->exec($errors[$index]['query'])) {
                                $errors[$index]['error'] = 'Process error: field `' . $field['Field'] . '` type could not be modified';
                            }
                        } else {
                            $errors[$index]['error'] = 'Database error: field `' . $field['Field'] . '` is `' . $field['Type'] . '` in table `' . $this->getTableName() . '`, but is `' . $structure['type'] . $structure['length'] . '` in class `' . get_called_class() . '`';
                            $errors[$index]['fix'] = 'Modify the type of the field. WARNING: This can cause data loss !';
                        }
                    }
                    if(($field['Null'] == 'NO' AND $structure['null'] == true) OR ($field['Null'] != 'NO' AND $structure['null'] == false)) {
                        $index = (isset($errors[$index])) ? $index + 1 : $index;
                        $errors[$index]['query'] = $this->getFieldStructure($fieldNameInClass, true);

                        if($repair) {
                            if(!$DBH->exec($errors[$index]['query'])) {
                                $errors[$index]['error'] = 'Process error: null option for field `' . $field['Field'] . '` could not be modified';
                            }
                        } else {
                            $errors[$index]['error'] = 'Database error: field `' . $field['Field'] . '` can ' . (($field['Null'] == 'NO') ? 'not be' : 'be') . ' null in table `' . $this->getTableName() . '`, but can ' . (($structure['null']) ? 'be' : 'not be') . ' null in class `' . get_called_class() . '`';
                            $errors[$index]['fix'] = 'Make the field `' . $field['Field'] . '` ' . (($structure['null']) ? 'NULL' : 'NOT NULL');
                        }
                    }
                    if((empty($field['Key']) AND $structure['index'] != null) OR (!empty($field['Key']) AND $structure['index'] == null)) {
                        $index = (isset($errors[$index])) ? $index + 1 : $index;
                        if($structure['index'] == null) {
                            $errors[$index]['query'] = 'ALTER TABLE `' . $this->getTableName() . '` DROP PRIMARY KEY';
                        } else {
                            $errors[$index]['query'] = 'ALTER TABLE `' . $this->getTableName() . '` ADD PRIMARY KEY(`' . $field['Field'] . '`)';
                        }

                        if($repair) {
                            if(!$DBH->exec($errors[$index]['query'])) {
                                $errors[$index]['error'] = 'Process error: field `' . $field['Field'] . '` keys could not be modified';
                            }
                        } else {
                            $errors[$index]['error'] = 'Database error: keys of field `' . $field['Field'] . '` in table `' . $this->getTableName() . '` doesn\'t match the class `' . get_called_class() . '`';
                            $errors[$index]['fix'] = 'Make the field `' . $field['Field'] . '` ' . (($structure['index'] == null) ? 'not `' . $field['Key'] . '`' : $structure['index']);
                        }
                    }
                    if($field['Default'] != $structure['default']) {
                        $index = (isset($errors[$index])) ? $index + 1 : $index;
                        $errors[$index]['query'] = $this->getFieldStructure($fieldNameInClass, true);

                        if($repair) {
                            if(!$DBH->exec($errors[$index]['query'])) {
                                $errors[$index]['error'] = 'Process error: field `' . $field['Field'] . '` default value could not be modified';
                            }
                        } else {
                            $errors[$index]['error'] = 'Database error: default values in table `' . $this->getTableName() . '` and in class `' . get_called_class() . '` doesn\'t match for field `' . $field['Field'] . '`';
                            $errors[$index]['fix'] = 'Modify the field default value';
                        }
                    }
                    if(($field['Extra'] == 'auto_increment' AND $structure['autoIncrement'] == false) OR ($field['Extra'] != 'auto_increment' AND $structure['autoIncrement'] == true)) {
                        $index = (isset($errors[$index])) ? $index + 1 : $index;
                        $errors[$index]['query'] = $this->getFieldStructure($fieldNameInClass, true);

                        if($repair) {
                            if(!$DBH->exec($errors[$index]['query'])) {
                                $errors[$index]['error'] = 'Process error: auto increment for field `' . $field['Field'] . '` could not be ' . (($structure['autoIncrement']) ? 'set' : 'removed');
                            }
                        } else {
                            $errors[$index]['error'] = 'Database error: field `' . $field['Field'] . '` is ' . (($field['Extra'] == 'auto_increment') ? 'set' : 'not set') . ' to auto increment in table `' . $this->getTableName() . '`, but is ' . (($structure['autoIncrement']) ? 'set' : 'not set') . ' to auto increment in class `' . get_called_class() . '`';
                            $errors[$index]['fix'] = (($structure['autoIncrement']) ? 'Set' : 'Remove') . ' auto increment for this field';
                        }
                    }
                }
            }

            foreach($this as $key => $value) {
                if(substr($key, 0, 5) != 'befew') {
                    if(!in_array($key, $fields)) {
                        $index = (isset($errors[$index])) ? $index + 1 : $index;
                        $errors[$index]['query'] = 'ALTER TABLE `' . $this->getTableName() . '`  ADD ' . $this->getFieldStructure($key);

                        if($repair) {
                            if(!$DBH->exec($errors[$index]['query'])) {
                                $errors[$index]['error'] = 'Process error: field `' . $field['Field'] . '` could not be added';
                            }
                        } else {
                            $errors[$index]['error'] = 'Database error: table `' . $this->getTableName() . '` is missing field `' . ((in_array($key, $this->befewLinks)) ? 'id_' . $key : $key) . '`';
                            $errors[$index]['fix'] = 'Add the field in the table';
                        }
                    }
                }
            }
        }

        return $errors;
    }

    public function getFieldStructure($field, $sql = false) {
        if(Utils::getVar($this->befewAttributes[$field]) == null) {
            $structure = $this->befewDefaultAttributes;
        } else {
            $structure = array_merge($this->befewDefaultAttributes, $this->befewAttributes[$field]);
        }

        if(Utils::getVar($structure['length']) == null) {
            $structure['length'] = Utils::getSQLDefaultLengthForType($structure['type']);
        }

        $structure['name'] = (in_array($field, $this->befewLinks)) ? 'id_' . $field : $field;

        if($sql) {
            return '`' . $structure['name'] . '` ' . $structure['type'] . $structure['length'] . ' ' . (($structure['null']) ? 'NULL' : 'NOT NULL') . ' DEFAULT \'' . $structure['default'] . '\'' . (($structure['autoIncrement']) ? 'AUTO_INCREMENT' : '') . (($structure['primary']) ? 'PRIMARY KEY' : '');
        } else {
            return $structure;
        }
    }

    public function createTable($query = false) {
        if($query = false && $this->isTableCreated() == false) {
            global $DBH;

            foreach($this as $key => $value) {
                if(substr($key, 0, 5) != 'befew') {
                    $current = &$this->befewAttributes[$key];

                    if (Utils::getVar($current) == null) {
                        $current = $this->befewDefaultAttributes;
                    }

                    foreach ($this->befewDefaultAttributes as $key2 => $value2) {
                        if (Utils::getVar($current[$key2]) == null) {
                            $current[$key2] = $value2;
                        }
                    }

                    if (strtolower($current['type']) == 'enum' OR strtolower($current['type']) == 'set' AND Utils::getVar($current['values']) != null) {
                        $current['length'] = $current['values'];
                    }
                }
            }

            $query = 'CREATE TABLE ' . $this->getTableName() . ' (' . "\n";
            $autoIncrement = false;
            $primary = null;
            $foreign = array();

            foreach($this->befewAttributes as $key => $value) {
                if(in_array($key, $this->befewLinks)) {
                    $query .= '    `id_' . $key . '` int(11),' . "\n";
                    $foreign[] = $key;
                } else {
                    if (Utils::getVar($value['length']) != null) {
                        $query .= '    `' . $key . '` ' . $value['type'] . '(' . $value['length'] . ') COLLATE ' . $value['collation'];
                    } else if (Utils::getSQLDefaultLengthForType($value['type']) != null) {
                        $query .= '    `' . $key . '` ' . $value['type'] . Utils::getSQLDefaultLengthForType($value['type']) . ' COLLATE ' . $value['collation'];
                    }

                    if ($value['null'] == false) {
                        $query .= ' NOT NULL';
                    }

                    if ($value['autoIncrement'] == true) {
                        $query .= ' AUTO_INCREMENT';
                        $autoIncrement = true;
                    }

                    if ($value['default'] != null) {
                        $query .= ' DEFAULT \'' . str_replace('\'', '\'\'', $value['default']) . '\'';
                    }

                    if ($value['comments'] != null) {
                        $query .= ' COMMENT \'' . str_replace('\'', '\'\'', $value['comments']) . '\'';
                    }

                    $query .= ',' . "\n";

                    if ($value['index'] == 'primary') {
                        $primary = $key;
                    }
                }
            }
            if($primary != null) {
                $query .= '    PRIMARY KEY (`' . $primary . '`)';

                if(count($foreign) > 0) {
                    $query .= ',';
                }

                $query .= "\n";
            }
            if(count($foreign) > 0) {
                if(count($foreign) == 1) {
                    $query .= '    FOREIGN KEY (`id_' . $foreign[0] . '`) REFERENCES `' . $foreign[0] . '`(`id`)' . "\n";
                } else {
                    for($i = 0; $i < count($foreign); $i++) {
                        $query .= '    FOREIGN KEY (`id_' . $foreign[$i] . '`) REFERENCES `' . $foreign[$i] . '`(`id`)';

                        if($i < (count($foreign) - 1)) {
                            $query .= ',';
                        }

                        $query .= "\n";
                    }
                }
            }

            $query .= ') ENGINE=InnoDB DEFAULT CHARSET=' . substr($this->getTableCollation(), 0, strpos($this->getTableCollation(), '_')) . ' COLLATE=' . $this->getTableCollation();

            if($autoIncrement) {
                $query .= ' AUTO_INCREMENT=1';
            }

            $query .= ';';

            if($query) {
                return $query;
            } else {
                try {
                    $DBH->query($query);
                    return true;
                } catch (\PDOException $e) {
                    if (DEBUG) {
                        Logger::error($e->errorInfo[2]);
                        Logger::error('For more informations, you can take a look at the query : ');
                        Logger::error($query);
                    }
                    return false;
                }
            }
        } else {
            return true;
        }
    }
}