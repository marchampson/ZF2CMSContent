<?php
namespace HootAndHoller\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
class MessagesTable extends TableGateway
{
	protected static $_tableName = 'messages';
	public function __construct(Adapter $adapter = NULL)
	{
		return parent::__construct(self::$_tableName, $adapter);
	}
	public static function setTableName($newName)
	{
		self::$_tableName = $newName;
	}
	public static function getTableName()
	{
		return self::$_tableName;
	}
	/**
	 * Inserts data into messages table
	 * @param string $sender = sender email
	 * @param string $text = message
	 * @param string $recipient = recipient email => "hoot"; if null => "holler"
	 */
	public function add($sender, $text, $recipient = NULL)
	{
		$insertArray['sender_email'] = $sender;
		$insertArray['message'] = $text;
		$insertArray['recipient_email'] = $recipient;
		return $this->insert($insertArray);
	}
	public function fetch(Select $select = NULL)
	{
		if ($select) {
			$result = $this->selectWith($select);
		} else {
			$result = $this->select();
		}
		return $result->toArray();
	}
}
