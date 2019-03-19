<?php

/**
 * 유닛테스트 출력 컨트롤러
 */
class Unittest extends CI_Controller
{
    public function index()
    {
        $oBlTodoTest = new BlTodoTest($this->unit);

        $oBlTodoTest->test_createTodo($this->getMockModel());
        $oBlTodoTest->test_getTodoList($this->getMockModel());
        $oBlTodoTest->test_updateTodo($this->getMockModel());
        $oBlTodoTest->test_completeTodo($this->getMockModel());
    }

    private function getMockModel()
    {
        $oMockModel = Mockery::mock(Model_todolist::class);
        $oMockModel->shouldReceive('startTrans')->andReturn(true)
            ->shouldReceive('completeTrans')->andReturn(true)
            ->shouldReceive('insertTodo')->andReturn('1')
            ->shouldReceive('clearRef')->andReturn(true)
            ->shouldReceive('insertTodoRef')->andReturn('1');

        return $oMockModel;
    }
}
