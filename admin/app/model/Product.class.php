<?php
/**
 * Project Active Record
 * @author  Jackson Meires
 */
class Product extends TRecord
{
    const TABLENAME = 'product';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('description');
        parent::addAttribute('url_repository');
        parent::addAttribute('login_repository');
        parent::addAttribute('password_repository');
        parent::addAttribute('logo');
        parent::addAttribute('dir_file_database');
        parent::addAttribute('name_file_database');

    }
    
    static public function getFirst()
    {
        $conn = TTransaction::get(); // get PDO connection
            
        // run query
        $result = $conn->query('SELECT min(id) as min from project');
        
        // show results 
        foreach ($result as $row) 
        { 
            return $row['min']; 
        } 
    }
    
    static public function listAll()
    {
        $repos = new TRepository('Project');
        return $repos->load(new TCriteria);
    }
}