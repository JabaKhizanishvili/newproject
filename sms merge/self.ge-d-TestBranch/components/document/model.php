<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class DocumentModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new DocumentTable();
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
	    $workers = (array) explode(',', C::_('WORKER', $data, ''));
//	    if (!count($workers)) {
//	        return false;
//        }

        if ( !$this->Table->bind( $data ) )
        {
            return false;
        }
        if ( !$this->Table->check() )
        {
            return false;
        }
        if ( !$this->Table->store() )
        {
            return false;
        }
        $id = $this->Table->insertid();

        if(!$this->SaveWorkersRel($id, $workers)) {
            return false;
        }
        return true;
	}

    public function SaveWorkersRel( $doc_id, $workers )
    {
        $DelQuery = 'delete '
            . ' from  rel_documents_uploads cp '
            . ' where '
            . ' cp.doc_id = ' . (int) $doc_id
        ;

        DB::Delete( $DelQuery );
        $query = 'Begin '
            . ' INSERT ALL ';
        foreach ( $workers as $worker )
        {
            if ( empty( $worker ) )
            {
                continue;
            }
            $query .= ' into rel_documents_uploads '
                . ' (doc_id, worker) '
                . 'values '
                . '('
                . (int) $doc_id
                . ','
                . (int) $worker
                . ')';
        }
        $query .= ' SELECT * FROM dual;'
            . 'end;';

        $Result = DB::InsertAll( $query );
        return $Result;

    }

}
