<?php
namespace ElmContent\Model;
use Zend\Db\TableGateway\TableGateway;

class UsersTable
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getUser($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function saveUser(User $user)
    {
        $data = array(
                'company' => $user->company,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => md5($user->password),
                'role' => $user->role,
                'phone' => $user->phone,
                'extension' => $user->extension
        );
    
        $id = (int)$user->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        
        return $id;
    }
    
    public function deleteUser($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}