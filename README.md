
## 개발환경
 
- AWS EC2 인스턴스 환경
- linux Ubuntu 16.04.5 LTS
- 웹서버 Apache 2.4
- 언어 PHP 7.1
- DB Mysql 5.7
 - 접속정보: host(13.113.51.202) user(my_user) passwd(asdf1234)






## 백엔드(RestAPI)

- 1) 할일목록 조회
 - 정의 : **GET** http://13.113.51.202/api/todos
 - 파라미터 
   - page : 현재 페이지
   - pageLimit : 한 페이지의 리스트

- 2) 할일등록

  - 정의 : **POST** http://13.113.51.202/api/todo

  - 파라미터 
    - todoName : 할일명
      
#### 3) 할일수정

- 정의 : **PUT** http://13.113.51.202/api/todo/{할일ID}

- 파라미터 : todoName (할일명)

#### 4) 할일완료

- 정의 : **PUT** http://13.113.51.202/api/todo/{할일ID}/complete


  
  
    
      
        
   
   
   
## 프론트엔드
  - vue.js 기반 SPA 구현