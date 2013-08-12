<?php
/*
class User extends Model {
	protected static $table_name = 'users';

	protected static $validates = array(
		'user_name' => 'required, length:6:20, alpha_begin, alpha_dash',
		'email' => 'required, email',
		'age' => 'integer'
	);
}

by extends the Model class, User will have all the instance methods of DB::table('users') as its static methods
	User::select($where); // DB::table('users')->select($where)
	User::delete($where); // DB::table('users')->delete($where)

and 3 dynamic methods which are getBy*, getOneBy*, deleteBy*.
Note that given a table field 'user_name', the according dynamic method name shall be exactly 'getByUserName', case sensitive!
	User::getByUserName('%aa%');
   	//DB::table('users')->select(array('user_name' => '%aa%'));
	
	User::getByUserNameAndSex('%aa%', '1');
   	//DB::table('users')->select(array('user_name' => '%aa%', 'sex' => 1));
	
	User::deleteByUserNameAndSexAndAge('%aa%', '1', array(15, 20, 25));
	//DB::table('users')->delete(array('user_name' => '%aa%', 'sex' => '1', 'age' => array(15, 20, 25)));

params in create and update operation will be automatically checked according to $validates
	User::create(array(
		'user_name' => 'user_name',
		'email' => 'a@a.com'
	));
	// create will succeed

	User::update(array(
		'user_name' => 'user_name',
		'age' => 'abc'
	), $where);
	// update will fail since age is not a valid integer
*/

/**
 * base model class
 *
 * @author lishuailong<lishl@bsatinfo.com>
 */
abstract class Model {
	protected static $table_name = '';
	protected static $validates = array();

	protected static $errors = array();

	public static function update(array $params, $where = null)
	{
		$validates = array_intersect_key(static::$validates, $params);
		if ( ! Validator::multiCheck($params, $validates, self::$errors))
		{
			return false;
		}

		return DB::table(static::$table_name)->update($params, $where);
	}

	public static function create(array $params)
	{
		if ( ! Validator::multiCheck($params, static::$validates, self::$errors))
		{
			return false;
		}

		return DB::table(static::$table_name)->insert($params);
	}

	public static function getError()
	{
		return self::$errors;
	}

	public static function __callStatic($method, $parameters)
	{
		if (preg_match('/^getOneBy(\w++)$/', $method, $matches)) 
		{
			$where = self::_dynamicWhere($matches[1], $parameters);
			return DB::table(static::$table_name)->selectOne($where);
		}

		if (preg_match('/^getBy(\w++)$/', $method, $matches)) 
		{
			$where = self::_dynamicWhere($matches[1], $parameters);
			return DB::table(static::$table_name)->select($where);
		}

		if (preg_match('/^deleteBy(\w++)$/', $method, $matches)) 
		{
			$where = self::_dynamicWhere($matches[1], $parameters);
			return DB::table(static::$table_name)->delete($where);
		}

		$db_query = DB::table(static::$table_name);
		return call_user_func_array(array($db_query, $method), $parameters);
	}

	private static function _dynamicWhere($by_field, $parameters)
	{
		$by_field = strtolower(preg_replace('/(?<!\b)(?=[A-Z])/', '_', $by_field));
		$fields = explode('_and_', $by_field);
		if (count($fields) !== count($parameters))
		{
			throw new Exception('parameters error!');
		}

		return array_combine($fields, $parameters);
	}
}