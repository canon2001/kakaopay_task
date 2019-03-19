<?php

/**
 * 비즈니스 로직 BlTodo class
 */
class BlTodo
{
    const ERROR_VALID_MESSAGE = '할일명이 잘못 되었습니다.';
    const ERROR_VALID_REF = '참조된 할일이 완료되지 않았습니다.';
    const ERROR_VALID_CODE = 422;

    const PARAM_TODO_ID = 'todoId';
    const PARAM_TODO_NAME = 'todoName';
    const PARAM_PAGE = 'page';
    const PARAM_PAGE_LIMIT = 'pageLimit';

    private $aParam, $oModel;

    /**
     * 요청값, 모델 주입
     *
     * @param array  $aRequsetParam 요청값
     * @param object $oModel        모델
     */
    public function init($aRequsetParam, $oModel)
    {
        $this->setParam($aRequsetParam);
        $this->setModel($oModel);
    }

    /**
     * 할일명 유효성 검사
     *
     * @param array $aParam 요청값
     *
     * @return bool
     * @throws Exception
     */
    public function validateTodoName($aParam)
    {
        if (mb_strlen(trim($aParam), 'utf-8') < 1) {
            throw new Exception (self::ERROR_VALID_MESSAGE, self::ERROR_VALID_CODE);
        }

        $sSpecialPattern = "/[` ~!#$%^&*|\\\'\";:\/?^=^+_()<>]/";
        if (preg_match($sSpecialPattern, $aParam)) {
            throw new Exception (self::ERROR_VALID_MESSAGE, self::ERROR_VALID_CODE);
        }

        return true;
    }

    /**
     * 할일 생성
     *
     * @return mixed
     * @throws Exception
     */
    public function createTodo()
    {
        $sTodoName = $this->getParam(self::PARAM_TODO_NAME);
        $this->validateTodoName($sTodoName);

        $this->getModel()->startTrans();
        $sLastID = $this->getModel()->insertTodo($sTodoName);
        if ($this->setTodoRef($sLastID, $sTodoName) === false) {
            throw new Exception (self::ERROR_VALID_MESSAGE, self::ERROR_VALID_CODE);
        }
        $sInsertedTodo = $this->getModel()->getTodo($sLastID);

        $this->getModel()->completeTrans();

        return $sInsertedTodo;
    }

    /**
     * 할일 조회
     *
     * @return array
     */
    public function getTodoList()
    {
        $sPage = $this->getParam(self::PARAM_PAGE);
        $sLimit = $this->getParam(self::PARAM_PAGE_LIMIT);

        $sPage = ($sPage < 1) ? 1 : $sPage;
        $iOffset = ($sPage - 1) * $sLimit;
        $aAllTodoList = $this->getModel()->getAllTodolist($iOffset, $sLimit);
        $iCountTodoList = $this->getModel()->getCountTodoList();

        return [$iCountTodoList, $aAllTodoList];
    }

    /**
     * 할일 수정
     *
     * @return mixed
     * @throws Exception
     */
    public function updateTodo()
    {
        $sTodoId = $this->getParam(self::PARAM_TODO_ID);
        $sTodoName = $this->getParam(self::PARAM_TODO_NAME);

        $this->validateTodoName($sTodoName);

        $this->getModel()->startTrans();

        $aUpdateData = [
            'todo_name' => $sTodoName,
            'mod_time'  => date("Y-m-d H:i:s")
        ];
        $this->getModel()->updateTodo($sTodoId, $aUpdateData);
        if ($this->setTodoRef($sTodoId, $sTodoName) === false) {
            throw new Exception (self::ERROR_VALID_MESSAGE, self::ERROR_VALID_CODE);
        }
        $aUpdatedTodo = $this->getModel()->getTodo($sTodoId);

        $this->getModel()->completeTrans();

        return $aUpdatedTodo;
    }

    /**
     * 할일 완료
     *
     * @return mixed
     * @throws Exception
     */
    public function completeTodo()
    {
        $sIdForComplete = $this->getParam(self::PARAM_TODO_ID);

        $this->getModel()->startTrans();
        if ($this->isPossibleComplete($sIdForComplete) === false) {
            throw new Exception (self::ERROR_VALID_REF, self::ERROR_VALID_CODE);
        }

        $this->getModel()->updateTodo($sIdForComplete, ['complete_time' => date("Y-m-d H:i:s")]);

        $sCompleteTodo = $this->getModel()->getTodo($sIdForComplete);

        $this->getModel()->completeTrans();

        return $sCompleteTodo;
    }

    /**
     * 참조 할일 설정
     *
     * @param string $sTodoId   참조 설정할 할일ID
     * @param string $sTodoName 할일명
     *
     * @return bool
     */
    private function setTodoRef($sTodoId, $sTodoName)
    {
        $this->getModel()->clearRef($sTodoId);

        $aRefList = explode('@', $sTodoName);
        array_shift($aRefList);

        if (count($aRefList) === 0) {
            return true;
        }

        foreach ($aRefList as $sRefId) {

            if (strpos($sRefId, '0') === 0 || is_numeric($sRefId) === false) {
                return false;
            }

            $this->getModel()->insertTodoRef($sTodoId, $sRefId);
        }

        return true;
    }

    /**
     * 할일 완료 가능한지 체크
     *
     * @param string $sIdForComplete 완료할 ID
     *
     * @return bool
     */
    private function isPossibleComplete($sIdForComplete)
    {
        $aRefList = $this->getModel()->getTodoByRefId($sIdForComplete);

        if (is_array($aRefList) === true && count($aRefList) === 0) {
            return true;
        }

        foreach ($aRefList as $aRefTodo) {
            if ($this->isPossibleComplete($aRefTodo['todo_id']) === false) {
                return false;
            }

            $aTodoData = $this->getModel()->getTodo($aRefTodo['todo_id']);

            if ($aTodoData['complete_time'] === null) {
                return false;
            }
        }

        return true;
    }

    private function setParam($aParam)
    {
        $this->aParam = $aParam;
    }

    private function getParam($sIndex = '')
    {
        if ($sIndex !== '') {
            return $this->aParam[$sIndex];
        }

        return $this->aParam;
    }

    private function setModel($oModel)
    {
        $this->oModel = $oModel;
    }

    private function getModel()
    {
        return $this->oModel;
    }

}
