<?php

namespace app\packages\Crud;

use Yii;
use yii\db\Exception;

class BaseModel extends Yii\db\ActiveRecord
{
    STATIC $db_link = 'db_maker';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%' . yii\helpers\Inflector::camel2id(
            yii\helpers\StringHelper::basename(get_called_class()),
            '_'
        ) . '}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get(self::$db_link);
    }

    /**
     * 执行原生sql
     * @param $sql
     * @param $params
     * @return bool|int
     * @throws Exception
     */
    public function executeQuery($sql, $params = [])
    {
        if (empty($sql)) {
            return false;
        }

        return self::getDb()->createCommand($sql, $params)->queryAll();
    }

    /**
     * 获取数据列表
     * @param $where array
     * @param $order string
     * @param $page int
     * @param $limit int
     * @return array|boolean
     */
    public function getList($where = [], $order = '', $page = 1, $limit = 20)
    {
        $_obj = self::find();
        if (isset($where['sql']) || isset($where['params'])) {
            $_obj->where($where['sql'], $where['params']);
        } else {
            if (is_array($where)) {
                $_obj->where($where);
            }
        }

        $_obj->orderBy($order);

        if (!empty($limit)) {
            $offset = max(($page - 1), 0) * $limit;
            $_obj->offset($offset)->limit($limit);
        }

        return $_obj->asArray(true)->all();
    }

    /**
     * 获取数据列表
     * @param $where array
     * @param $order string
     * @return array|boolean
     */
    public function getOneByOrder($where = [], $order = '')
    {
        $_obj = self::find();
        if (isset($where['sql']) || isset($where['params'])) {
            $_obj->where($where['sql'], $where['params']);
        } else {
            if (is_array($where)) {
                $_obj->where($where);
            }
        }

        $_obj->orderBy($order)->offset(0)->limit(1);

        return $_obj->asArray(true)->one();
    }

    /**
     * 获取信息
     * @param $where array
     * @return array|boolean
     */
    public function getOne($where = [])
    {
        if (empty($where)) {
            return false;
        }

        $obj = self::find()->where($where);
        if (!empty($obj)) {
            return $obj->asArray(true)->one();
        }

        return false;
    }

    /**
     * @param $data
     * @param string $db_link
     * @return bool|int
     * @throws Exception
     */
    public function insertData($data, $db_link = '')
    {
        if (empty($data)) {
            return false;
        }

        if (empty($db_link)) {
            $db_link = self::getDb();
        }

        return $db_link->createCommand()->insert(self::tableName(), $data)->execute();
    }

    /**
     * 插入数据，如果存在则跳过(需要设置唯一索引)
     * @param $data
     * @param $db_link
     * @return int
     * @throws Exception
     */
    public function insertIgnore($data, $db_link = '')
    {
        if (!$data || !is_array($data)) {
            return false;
        }

        foreach ($data as $key => $val) {
            $keys[]   = '`' . $key . '`';
            $values[] = "'" . mysql_escape_string($val) . "'";
        }

        $sql = "INSERT IGNORE INTO " . self::tableName() . ' (' . implode(', ', $keys) . ') VALUES (' . implode(
                ', ',
                $values
            ) . ')';

        if (empty($db_link)) {
            $db_link = self::getDb();
        }

        return $db_link->createCommand($sql)->execute();
    }

    /**
     * 插入数据，如果存在则替换(需要设置唯一索引)
     * @param $data
     * @param $db_link
     * @return int
     * @throws Exception
     */
    public function insertReplace($data, $db_link = '')
    {
        if (!$data || !is_array($data)) {
            return false;
        }

        foreach ($data as $key => $val) {
            $keys[]   = '`' . $key . '`';
            $values[] = "'" . mysql_escape_string($val) . "'";
        }

        $sql = "REPLACE INTO " . self::tableName() . ' (' . implode(', ', $keys) . ') VALUES (' . implode(
                ', ',
                $values
            ) . ')';
        if (empty($db_link)) {
            $db_link = self::getDb();
        }

        return $db_link->createCommand($sql)->execute();
    }

    /**
     * 更新记录
     * @param $data
     * @param $condition
     * @param array $params
     * @param string $db_link
     * @return bool|int
     * @throws Exception
     */
    public function updateData($data, $condition, $params = [], $db_link = '')
    {
        if (empty($condition)) {
            return false;
        }

        if (empty($db_link)) {
            $db_link = self::getDb();
        }

        return $db_link->createCommand()->update(self::tableName(), $data, $condition, $params)->execute();
    }

    /**
     * 获取总条数
     * @param $where array
     * @return int
     */
    public function getQueryCount($where = [])
    {
        $_obj = self::find();
        if (isset($where['sql']) || isset($where['params'])) {
            $_obj->where($where['sql'], $where['params']);
        } else {
            $_obj->where($where);
        }

        return intval($_obj->count());
    }

    /**
     * 获取某字段的和
     * @param $field_name
     * @param array $condition
     * @param array $params
     * @return int
     */
    public function getFieldSum($field_name, $condition = [], $params = [])
    {
        if (empty($field_name)) {
            return false;
        }

        return self::find()->select('sum(' . $field_name . ')')->where($condition, $params)->scalar();
    }

    /**
     * 删除记录
     * @param $condition
     * @param array $params
     * @param string $db_link
     * @return bool|int
     */
    public function deleteByCondition($condition, $params = [], $db_link = '')
    {
        if (!empty($condition)) {
            if (empty($db_link)) {
                $db_link = self::getDb();
            }

            try {
                return $db_link->createCommand()->delete(self::tableName(), $condition, $params)->execute();
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * 关联模型查询（需要在主model中定义关联模式）
     * @param string $fields
     * @param $model_and_join_type
     * @param array $where
     * @param array $and_where
     * @param string $order_by
     * @param int $page
     * @param int $limit
     * @param boolean $get_count
     * @return array|yii\db\ActiveRecord[]
     */
    public function getRelationList(
        $fields = '*',
        $model_and_join_type = [],
        $where = [],
        $and_where = [],
        $order_by = '',
        $page = 1,
        $limit = 20,
        $get_count = false
    ) {
        $count = 0;
        $obj   = self::find()->select($fields);

        if (!empty($where)) {
            $obj = $obj->where($where);
        }

        if (!empty($and_where)) {
            $obj = $obj->andWhere($and_where);
        }

        foreach ($model_and_join_type as $model_name => $join) {
            if (in_array($join, ['with', 'joinWith'])) {
                $obj = $obj->$join($model_name);
            }
        }
        if ($get_count === true) {
            $count = $obj->count();
        }

        if (!empty($order_by)) {
            $obj = $obj->orderBy($order_by);
        }
        if (!empty($page) && $limit > 0) {
            $offset = ($page - 1) * $limit;
            $obj    = $obj->offset($offset)->limit($limit);
        }
        $data = $obj->asArray()->all();

        return ['items' => $data, 'count' => $count];
    }

    /**
     * 关联模型查询（需要在主model中定义关联模式）
     * @param string $fields
     * @param $model_and_join_type
     * @param array $where
     * @param array $and_where
     * @return array|yii\db\ActiveRecord[]
     */
    public function getRelationOne($fields = '*', $model_and_join_type = [], $where = [], $and_where = [])
    {
        $obj = self::find()->select($fields);

        if (!empty($where)) {
            $obj = $obj->where($where);
        }

        if (!empty($and_where)) {
            $obj = $obj->andWhere($and_where);
        }

        if (!empty($model_and_join_type)) {
            foreach ($model_and_join_type as $model_name => $type) {
                if (!in_array($type, ['with', 'joinWith'])) {
                    return false;
                }
                $obj = $obj->$type($model_name);
            }
        }

        if (!empty($order_by)) {
            $obj = $obj->orderBy($order_by);
        }
        $data = $obj->asArray(true)->one();

        return $data;
    }
}