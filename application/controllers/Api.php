<?php

/**
 * Api 컨트롤러
 */
class Api extends CI_Controller
{
    const MODEL_CLASS_NAME = 'model_todolist';
    const CONTENT_TYPE = 'application/json';
    const STATUS_CODE_SUCCESS = 200;

    private $oBlTodo;

    function __construct()
    {
        parent::__construct();
        $this->oBlTodo = new BlTodo();
        $this->load->add_package_path(APPPATH . 'bl', false);
        $this->load->model(self::MODEL_CLASS_NAME);
    }

    /**
     * 할일 등록 (POST /api/todo 와 맵핑)
     */
    public function todo()
    {
        try {
            $this->checkMethod(POST_METHOD);
            $this->oBlTodo->init($this->requestParam(), $this->model_todolist);
            $sInsertedTodo = $this->oBlTodo->createTodo();
            $this->setOutput(['todo' => $sInsertedTodo]);
        } catch (Exception $e) {
            $this->setOutput($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 할일 페이지 리스트 (GET /api/todos 와 맵핑)
     */
    public function todos()
    {
        try {
            $this->checkMethod(GET_METHOD);
            $this->oBlTodo->init($this->requestParam(), $this->model_todolist);
            list($iCountTodoList, $aAllTodoList) = $this->oBlTodo->getTodoList();
            $this->setOutput(['total' => $iCountTodoList, 'todo_list' => $aAllTodoList]);
        } catch (Exception $e) {
            $this->setOutput($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 할일 수정 (PUT /api/todo/{ID} 와 맵핑)
     *
     * @param string $sTodoId 수정할 아이디
     */
    public function updateTodo($sTodoId)
    {
        try {
            $this->checkMethod(PUT_METHOD);
            $aRequestParam = $this->requestParam();
            $this->oBlTodo->init(array_merge(['todoId' => $sTodoId], $aRequestParam), $this->model_todolist);
            $aUpdatedTodo = $this->oBlTodo->updateTodo();
            $this->setOutput(['todo' => $aUpdatedTodo]);
        } catch (Exception $e) {
            $this->setOutput($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 할일 완료 (PUT /api/todo/{ID}/complete 와 맵핑)
     *
     * @param string $sTodoId 수정할 아이디
     */
    public function completeTodo($sTodoId)
    {
        try {
            $this->checkMethod(PUT_METHOD);
            $this->oBlTodo->init(['todoId' => $sTodoId], $this->model_todolist);
            $sCompleteTodo = $this->oBlTodo->completeTodo();
            $this->setOutput(['todo' => $sCompleteTodo]);
        } catch (Exception $e) {
            $this->setOutput($e->getMessage(), $e->getCode());
        }
    }

    /**
     * api 결과 출력
     *
     * @param array $aOutput     출력 data
     * @param int   $iStatusCode 출력 상태코드
     */
    private function setOutput($aOutput, $iStatusCode = self::STATUS_CODE_SUCCESS)
    {
        if ($iStatusCode !== self::STATUS_CODE_SUCCESS) {
            $aOutput = ['error' => $aOutput];
        }

        $this->output->set_content_type(self::CONTENT_TYPE)
            ->set_status_header($iStatusCode)
            ->set_output(json_encode($aOutput));
    }

    /**
     * HTTP 메소드 체크
     *
     * @param string $sMethod 요청된 HTTP메소드
     *
     * @return bool
     * @throws Exception
     */
    private function checkMethod($sMethod)
    {
        if ($_SERVER["REQUEST_METHOD"] !== $sMethod) {
            throw new Exception('Method Not Allowed', 405);
        }

        return true;
    }

    /**
     * 요청된 파라미터 리턴
     *
     * @return mixed
     * @throws Exception
     */
    private function requestParam()
    {
        if ($_SERVER["REQUEST_METHOD"] === GET_METHOD) {
            return $this->input->get();
        }

        $aRequestParam = @json_decode($this->input->raw_input_stream, 1);
        if ($aRequestParam === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Bad Request', 400);
        }

        return $aRequestParam;
    }
}
