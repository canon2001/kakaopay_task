<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>[v-clock] { display : none; }</style>
</head>
<body>

<div class="container-fluid" id="container_todolist">
    <div v-clock>
    <div class="py-5 text-center">
        <h2>[김순철] 서버개발자 사전과제</h2>
    </div>

    <div class="form-group">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#registTodoModal" v-on:click="registBtnClick">할일등록</button>
    </div>

    <table class="table text-center">
        <thead class="thead-light">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">할일</th>
            <th scope="col">등록일</th>
            <th scope="col">수정일</th>
            <th scope="col">완료일</th>
            <th scope="col">수정</th>
            <th scope="col">상태</th>
        </tr>
        </thead>
        <tbody v-if="data_todoList.length !== 0">
            <tr v-for="data in data_todoList" >
                <th scope="row">{{ data.id }}</th>
                <td>{{ data.todo_name }}</td>
                <td>{{ data.add_time }}</td>
                <td>{{ data.mod_time }}</td>
                <td>
                    <span v-if="data.complete_time === null">-</span>
                    <span v-else>{{ data.complete_time }}</span>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#updateTodoModal" v-on:click="updateBtnClick(data.id, data.todo_name)">수정하기</button>
                </td>
                <td>
                    <span v-if="data.complete_time === null">
                        <button type="button" class="btn btn-outline-secondary" v-on:click="completeBtnClick(data.id, data.todo_name)">완료처리</button>
                    </span>
                    <span v-else>
                        완료
                    </span>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="7" style="text-align: center;height: 70px;vertical-align: middle;">No List</td></tr>
        </tbody>
    </table>

    <div class="modal fade" id="registTodoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span>할일등록</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        <label for="firstName">할일명</label>
                        <input type="text" class="form-control" v-model="saveTodoName">
                        <p>
                            <small>공백 및 특수문자 사용 불가 (할일 참조시 @ 사용)</small></br>
                            <small>할일 참조는 숫자만 가능 ex)할일@1</small>
                        </p>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click="saveClickInModal">Save</button>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" ref="rModalClose">Close</button>
                </div>
            </div>
        </div>
    </div>
        <div class="modal fade" id="updateTodoModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span>할일수정 (ID : {{ updateTodoId }})</span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                            <label for="firstName">할일명</label>
                            <input type="text" class="form-control" v-model="saveTodoName">
                            <p>
                                <small>공백 및 특수문자 사용 불가 (할일 참조시 @ 사용)</small></br>
                                <small>할일 참조는 숫자만 가능 ex)할일@1</small>
                            </p>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-on:click="updateClickInModal">Update</button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" ref="uModalClose">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <div style="margin-top: 70px">
        <paginate
            :page-count="pageTotal"
            :page-range="10"
            :margin-pages="3"
            :click-handler="clickPageNum"
            :prev-class="'page-item'"
            :next-class="'page-item'"
            :prev-link-class="'page-link'"
            :next-link-class="'page-link'"
            :prev-text="'<'"
            :next-text="'>'"
            :container-class="'pagination justify-content-center'"
            :page-class="'page-item'"
            :page-link-class="'page-link'">
        </paginate>
    </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" ></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script src="/resources/axios-v0.18.0.js"></script>
<script src="/resources/paginate-v2.1.js"></script>
<script src="/resources/todolist.js"></script>

</body>
</html>
