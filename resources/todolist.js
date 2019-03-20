const API_URL = {
    TODOS: '/api/todos',
    TODO: '/api/todo'
}

/**
 * vue 페이징 컴포넌트
 */
Vue.component('paginate', VuejsPaginate)

/**
 * vue 인스턴스
 */
const searchConditionVm = new Vue({

    el: '#container_todolist',
    data: {
        saveTodoName: '',
        updateTodoId: null,
        pageTotal: 1,
        page: 1,
        pageLimit: 5,
        data_todoList: []
    },
    methods: {

        /**
         * 할일 리스트 가져오기
         */
        getTodoList: function () {
            let self = this;
            axios.get(API_URL.TODOS, {
                params: {
                    'page': this.page,
                    'pageLimit': this.pageLimit
                }
            }).then(function (oResponse) {
                if (oResponse.data.hasOwnProperty('todo_list') === false) {
                    return false;
                }
                if (oResponse.data.hasOwnProperty('total') === false) {
                    return false;
                }

                self.data_todoList = oResponse.data.todo_list;
                self.pageTotal = Math.ceil(oResponse.data.total / self.pageLimit);

            }).catch(function (error) {
                if (error.response.data.hasOwnProperty('error') === true) {
                    alert(error.response.data.error);
                }
                console.log(error);
            });
        },

        /**
         * 할일등록 버튼 클릭
         */
        registBtnClick: function () {
            this.saveTodoName = '';
        },

        /**
         * 할일등록 모달에서 저장버튼
         */
        saveClickInModal: function () {
            let self = this;
            axios.post(API_URL.TODO, {
                'todoName': this.saveTodoName,
            }).then(function (oResponse) {
                alert('할일(' + self.saveTodoName + ')이 등록되었습니다.');
                self.getTodoList();
                self.$refs.rModalClose.click();
            }).catch(function (error) {
                if (error.response.data.hasOwnProperty('error') === true) {
                    alert(error.response.data.error);
                }
                console.log(error);
            });
        },

        /**
         * 할일수정 모달에서 수정버튼
         */
        updateClickInModal: function () {
            if (confirm('수정 하시겠습니까?') === false) {
                return false;
            }

            let self = this;
            if(this.updateTodoId === null) {
                alert('오류');
                return false;
            }
            axios.put(API_URL.TODO + '/' + this.updateTodoId, {
                'todoName': this.saveTodoName,
            }).then(function (oResponse) {
                alert('할일(' + self.saveTodoName + ')이 수정되었습니다.');
                self.getTodoList();
                self.$refs.uModalClose.click();
            }).catch(function (error) {
                if (error.response.data.hasOwnProperty('error') === true) {
                    alert(error.response.data.error);
                }
                console.log(error);
            });
        },

        /**
         * 수정하기 버튼
         * @param sTodoId
         * @param sTodoName
         */
        updateBtnClick: function (sTodoId, sTodoName) {
            this.updateTodoId = sTodoId;
            this.saveTodoName = sTodoName;
        },

        /**
         * 완료하기 버튼
         * @param sTodoId
         */
        completeBtnClick: function (sTodoId, sTodoName) {
            if (confirm('(ID : ' +sTodoId+') 완료처리 하시겠습니까?') === false) {
                return false;
            }

            let self = this;
            axios.put(API_URL.TODO + '/' + sTodoId + '/complete', {
                'todoId': sTodoId,
            }).then(function (oResponse) {
                alert('ID : ' + sTodoId +'\n할일(' + sTodoName + ')이 완료 되었습니다.');
                self.getTodoList();
            }).catch(function (error) {
                if (error.response.data.hasOwnProperty('error') === true) {
                    alert(error.response.data.error);
                }
                console.log(error);
            });
        },

        /**
         * 페이지 클릭
         * @param iPageNum
         */
        clickPageNum: function (iPageNum) {
            this.page = iPageNum;
            this.getTodoList();
        },

        /**
         * 모달닫기
         */
        closeModal: function () {
            this.$refs.uModalClose.click();
        }
    },


    mounted: function () {
        this.$nextTick(function () {
            this.getTodoList();
        });
    },

});
