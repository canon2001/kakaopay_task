<?php

class Model_todolist extends CI_Model
{
    const TABLE_TODO_LIST = 'todo_list';
    const TABLE_TODO_REF = 'todo_ref';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 트랜잭션 start
     */
    public function startTrans()
    {
        $this->db->trans_start();
    }

    /**
     * 트랜잭션 complete
     */
    public function completeTrans()
    {
        $this->db->trans_complete();
    }

    /**
     * 트랜잭션 complete
     */
    public function rollbackTrans()
    {
        $this->db->trans_rollback();
    }

    /**
     * 할일 단건조회
     *
     * @param $sTodoId
     *
     * @return mixed
     */
    public function getTodo($sTodoId)
    {
        $this->db->where("id", $sTodoId);
        $oQuery = $this->db->get(self::TABLE_TODO_LIST);
        return $oQuery->row_array();
    }

    /**
     * 할일 전체조회
     *
     * @param $iOffset
     * @param $sLimit
     *
     * @return mixed
     */
    public function getAllTodolist($iOffset, $sLimit)
    {
        $this->db->order_by("id", "desc");
        $oQuery = $this->db->get(self::TABLE_TODO_LIST, $sLimit, $iOffset);

        return $oQuery->result_array();
    }

    /**
     * 할일 전체 카운트
     *
     * @return mixed
     */
    public function getCountTodoList()
    {
        return $this->db->count_all_results(self::TABLE_TODO_LIST);
    }

    /**
     * 할일 insert
     *
     * @param $sTodoName
     *
     * @return mixed
     */
    public function insertTodo($sTodoName)
    {
        $sDate = date("Y-m-d H:i:s");
        $aInsertData = [
            'todo_name' => $sTodoName,
            'add_Time'  => $sDate,
            'mod_time'  => $sDate
        ];

        $this->db->insert(self::TABLE_TODO_LIST, $aInsertData);

        return $this->db->insert_id();
    }

    /**
     * 할일 update
     *
     * @param $sTodoName
     *
     * @return mixed
     */
    public function updateTodo($sTodoId, $aUpdateData)
    {

        $this->db->where('id', $sTodoId);
        return $this->db->update(self::TABLE_TODO_LIST, $aUpdateData);
    }

    /**
     * 참조할일 전체 삭제
     *
     * @param $sTodoId
     *
     * @return mixed
     */
    public function clearRef($sTodoId)
    {
        return $this->db->delete(self::TABLE_TODO_REF, ['todo_id' => $sTodoId]);
    }

    /**
     * 참조 할일 등록
     *
     * @param $sTodoId
     * @param $sRefId
     *
     * @return mixed
     */
    public function insertTodoRef($sTodoId, $sRefId)
    {
        $aInsertData = [
            'todo_id' => $sTodoId,
            'ref_id'  => $sRefId,
        ];

        $this->db->insert(self::TABLE_TODO_REF, $aInsertData);

        return $this->db->insert_id();
    }

    /**
     * 참조된ID 조회
     *
     * @param $sIdForComplete
     *
     * @return mixed
     */
    public function getTodoByRefId($sIdForComplete)
    {
        $this->db->where("ref_id", $sIdForComplete);
        $oQuery = $this->db->get(self::TABLE_TODO_REF);
        return $oQuery->result_array();
    }
}
