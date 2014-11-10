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
            return false;
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

    public function createTable() {
        if($this->isTableCreated() == false) {
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

            try {
                $DBH->query($query);
                return true;
            } catch (\PDOException $e) {
                if(DEBUG) {
                    Logger::error($e->errorInfo[2]);
                    Logger::error('For more informations, you can take a look at the query : ');
                    Logger::error($query);
                }
                return false;
            }
        } else {
            return true;
        }
    }
}