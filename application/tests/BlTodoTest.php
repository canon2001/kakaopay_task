<?php

class BlTodoTest
{
    private $oBlTodo, $oPhpUnit;

    public function __construct($oPhpUnit)
    {
        $this->oBlTodo = new BlTodo();
        $this->oPhpUnit = $oPhpUnit;
    }

    /**
     * 할일 생성
     */
    public function test_createTodo($oMockModel)
    {
        $aExpectSuccess = [
            'id'            => '1',
            'todo_name'     => 'exTodo',
            'add_time'      => '2019-03-19 14:43:17',
            'mod_time'      => '2019-03-19 14:43:17',
            'complete_time' => null,
        ];

        $aProvideParam = [
            [['todoName' => 'exTodo$^&*'], 422, '#할일등록# 할일 특수문자'],
            [['todoName' => 'exTodo@char'], 422, '#할일등록# 할일 참조값 문자'],
            [['todoName' => 'exTodo@char4325'], 422, '#할일등록# 할일 참조값 문자+숫자'],
            [['todoName' => 'exTodof@05'], 422, '#할일등록# 할일 참조값 0으로 시작하는 숫자'],
            [['todoName' => 'exTodo@2'], $aExpectSuccess, '#할일등록# 할일 참조값 숫자'],
            [['todoName' => 'exTodo@2@3'], $aExpectSuccess, '#할일등록# 할일 참조값 숫자 여러개'],
            [['todoName' => 'exTodo'], $aExpectSuccess, '#할일등록# 할일 참조값 없음']
        ];

        $oMockModel->shouldReceive('getTodo')->andReturn($aExpectSuccess);

        foreach ($aProvideParam as $aParam) {

            try {
                $this->oBlTodo->init($aParam[0], $oMockModel);
                $oResult = $this->oBlTodo->createTodo();

            } catch (Exception $e) {
                echo $this->oPhpUnit->run($e->getCode(), $aParam[1], $aParam[2]);
                continue;
            }
            echo $this->oPhpUnit->run($oResult, $aParam[1], $aParam[2]);
        }
    }

    /**
     * 할일 조회
     */
    public function test_getTodoList($oMockModel)
    {
        $iExpectCount = 2;
        $aExpectTodoList = [
            [
                'id'            => '2',
                'todo_name'     => 'exTodo@1',
                'add_time'      => '2019-03-18 20:00:51',
                'mod_time'      => '2019-03-19 14:25:22',
                'complete_time' => '2019-03-18 20:27:53',
            ],
            [
                'id'            => '1',
                'todo_name'     => 'exTodo@4@5',
                'add_time'      => '2019-03-18 19:44:40',
                'mod_time'      => '2019-03-18 20:03:36',
                'complete_time' => '2019-03-18 20:25:04',
            ]
        ];

        $oMockModel->shouldReceive('getAllTodolist')->andReturn($aExpectTodoList);
        $oMockModel->shouldReceive('getCountTodoList')->andReturn($iExpectCount);

        $this->oBlTodo->init(['page' => '1', 'pageLimit' => 2], $oMockModel);
        list($iCountTodoList, $aTodoList) = $this->oBlTodo->getTodoList();

        echo $this->oPhpUnit->run($aTodoList, $aExpectTodoList, "#할일 리스트#");
        echo $this->oPhpUnit->run($iCountTodoList, $iExpectCount, "#할일 리스트# 전체 카운트");
    }


    /**
     * 할일 수정
     */
    public function test_updateTodo($oMockModel)
    {
        $aExpectSuccess = [
            'id'            => '1',
            'todo_name'     => 'exTodo',
            'add_time'      => '2019-03-19 14:43:17',
            'mod_time'      => '2019-03-19 14:45:09',
            'complete_time' => null,
        ];

        $aProvideParam = [
            [['todoName' => 'exTodo$^&*'], 422, '#할일수정# 할일 특수문자'],
            [['todoName' => 'exTodo@char'], 422, '#할일수정# 할일 참조값 문자'],
            [['todoName' => 'exTodo@char4325'], 422, '#할일수정# 할일 참조값 문자+숫자'],
            [['todoName' => 'exTodof@05'], 422, '#할일수정# 할일 참조값 0으로 시작하는 숫자'],
            [['todoName' => 'exTodo@2'], $aExpectSuccess, '#할일수정# 할일 참조값 숫자'],
            [['todoName' => 'exTodo@2@3'], $aExpectSuccess, '#할일수정# 할일 참조값 숫자 여러개'],
            [['todoName' => 'exTodo'], $aExpectSuccess, '#할일수정# 할일 참조값 없음']
        ];

        $oMockModel->shouldReceive('getTodo')->andReturn($aExpectSuccess);
        $oMockModel->shouldReceive('updateTodo')->andReturn(true);

        foreach ($aProvideParam as $aParam) {
            try {
                $this->oBlTodo->init(array_merge(['todoId' => '1'], $aParam[0]), $oMockModel);
                $oResult = $this->oBlTodo->updateTodo();

            } catch (Exception $e) {
                echo $this->oPhpUnit->run($e->getCode(), $aParam[1], $aParam[2]);
                continue;
            }

            echo $this->oPhpUnit->run($oResult, $aParam[1], $aParam[2]);
        }
    }

    /**
     * 할일 완료
     */
    public function test_completeTodo()
    {
        $aExpectSuccess = [
            'id'            => '1',
            'todo_name'     => 'exTodo',
            'add_time'      => '2019-03-19 14:43:17',
            'mod_time'      => '2019-03-19 14:43:17',
            'complete_time' => '2019-03-20 11:33:53',
        ];


        $aProvideParam = [
            [
                'request'  => ['todoId' => 1],
                'refList'  => [
                    [
                        ['todo_id' => '2'],
                        ['todo_id' => '3'],
                        ['todo_id' => '4']
                    ],
                    []
                ],
                'todoData' => [
                    'refData' => ['complete_time' => null],
                    'result'  => 422
                ],
                'testName' => '#할일 완료# exception 완료되지 않은 참조된 할일(2,3,4)이 존재'
            ],
            [
                'request'  => ['todoId' => 1],
                'refList'  => [
                    [
                        ['todo_id' => '2'],
                        ['todo_id' => '3'],
                        ['todo_id' => '4']
                    ],
                    []
                ],
                'todoData' => [
                    'refData' => ['complete_time' => '2019-03-20 11:33:53'],
                    'refData' => ['complete_time' => null],

                    'result' => 422
                ],
                'testName' => '#할일 완료# exception 참조할일(2)는 완료, 참조된 할일(3)이 완료되지 않음'
            ],
            [
                'request'  => ['todoId' => 2],
                'refList'  => [
                    []
                ],
                'todoData' => [
                    'result' => $aExpectSuccess
                ],
                'testName' => '#할일 완료# success 참조된 할일이 존재하지 않아 바로 완료'
            ],

            [
                'request'  => ['todoId' => 3],
                'refList'  => [
                    [
                        ['todo_id' => '4']
                    ],
                    []
                ],
                'todoData' => [
                    'refData' => ['complete_time' => null],
                    'result'  => 422
                ],
                'testName' => '#할일 완료# exception 완료되지 않은 참조된 할일(4)이 존재'
            ],
            [
                'request'  => ['todoId' => 3],
                'refList'  => [
                    [
                        ['todo_id' => '4']
                    ],
                    []
                ],
                'todoData' => [
                    'refData' => ['complete_time' => '2019-03-20 11:33:53'],
                    'result'  => $aExpectSuccess
                ],
                'testName' => '#할일 완료# success 참조된 할일(4)이 완료되어 할일(3)완료'
            ],
        ];


        foreach ($aProvideParam as $aParam) {
            try {
                $oMockModel = Mockery::mock(Model_todolist::class);
                $oMockModel->shouldReceive('startTrans', 'completeTrans')->andReturn(true);

                $this->oBlTodo->init($aParam['request'], $oMockModel);
                $oMockModel->shouldReceive('getTodoByRefId')->andReturnValues($aParam['refList']);
                $oMockModel->shouldReceive('getTodo')->andReturnValues($aParam['todoData']);
                $oMockModel->shouldReceive('updateTodo')->andReturn(true);

                $sCompleteTodo = $this->oBlTodo->completeTodo();

            } catch (Exception $e) {
                echo $this->oPhpUnit->run($e->getCode(), $aParam['todoData']['result'], $aParam['testName']);
                continue;
            }

            echo $this->oPhpUnit->run($sCompleteTodo, $aParam['todoData']['result'], $aParam['testName']);

        }
    }
}
