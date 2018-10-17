# **api说明**

## **消息结构**

返回值总是一个包含三个字段的json  
三个字段分别是  
- code
- message
- data

### **code**
整型数据：  
  
请求成功：`REQUEST_SUCCESS = 0`  
  
服务器未开始运行：`ERR_SERVER_NOT_WORKING = -1`  
  
缺少参数：`ERR_MISSING_PARAM = 12000`  
未知命令：`ERR_UNKNOWN_REQUEST = 14000`  
权限不足：`ERR_UNAUTHED = 20000`  
  
请求失败：`REQUEST_FAILED = 1000`  
请求禁止：`REQUEST_OBJECT_NOT_EXSIST = 2000`  
请求对象不存在：`REQUEST_FORBIDDEN = 10000`  
  
### **message**
字符串，一段相关消息

### **data**
json编码的数据

-----

## **接口**

### **`/login`**
#### 登陆接口
参数：
- `account` 账号
- `password` 密码（hash后
  
权限：`none`  
返回示例：
```json
{
    "code": 0,
    "message": "登陆成功",
    "data": {
        "id": "01A17101",
        "name": "楼琦珍",
        "auth": "student"
    }
}
```
### **`/logout`**
#### 注销接口
参数：`none`  
权限：`none`  
返回示例：
```json
{
    "code": 0,
    "message": "注销成功",
    "data": []
}
```
### **`/appeal`**
#### 申诉接口
参数：
- `account` 账号
- `content` 申诉内容
  
权限：`none`  
返回示例：（返回申诉id）
```json
{
    "code": 0,
    "message": "申诉成功",
    "data": "859817"
}
```
### **`/query_appeal`**
#### 申诉接口
参数：
- `account` 账号
- `number` 申诉号
  
权限：`none`  
返回示例：
```json
{
    "code": 0,
    "message": "查询成功",
    "data": {
        "id": "859817",
        "account": "01A17103",
        "message": "申诉的具体内容",
        "logs": [
            "2018-08-09 16:21:52 : Appeal created"
        ]
    }
}
```

### **`/change_my_password`**
#### 修改密码接口
参数：
- `prevpass` 旧密码
- `newpass` 新密码  

权限：`login`  
返回示例：
```json
{
    "code": 0,
    "message": "修改成功",
    "data": []
}
```

### **`/check_status`**
#### 学生状态接口
参数：`none`  
权限：`student`  
返回示例：
```json
{
    "code": 0,
    "message": "请求成功",
    "data": {
        "id": "01A17102",
        "status": 100,
        "starttime": 0,
        "submittime": 0,
        "answersheet": [],
        "score": 0,
        "servertime": 1533803447
    }
}
```
`status`：
- `100` 未开始
- `200` 考试中
- `400` 已结束
  
### **`/load_test`** 
#### 加载试卷接口
参数：`none`  
权限：`student`  
返回示例：
```json
{
    "code": 0,
    "message": "请求成功",
    "data": {
        "1": {
            "question": "随机单选题99--答案3",
            "category": 1,
            "options": {
                "1": "选项2",
                "2": "选项4",
                "3": "选项3",
                "4": "选项1"
            }
        },
        "2": {
            "question": "随机单选题37--答案3",
            "category": 1,
            "options": {
                "1": "选项3",
                "2": "选项1",
                "3": "选项4",
                "4": "选项2"
            }
        },
        "3": {
            "question": "随机判断题31--答案错误",
            "category": 0
        },
	[...]
    }
}
```
### **`/submit_one`**
#### 提交一个答案接口
参数：
- `order` 题号
- `answer` 答案

权限：`student`  
返回示例：
```json
{
    "code": 0,
    "message": "提交成功",
    "data": {
        "id": "01A17102",
        "status": 200,
        "starttime": 1533803761,
        "submittime": 0,
        "answersheet": {
            "1": "3",
            "2": "1"
        },
        "score": 0,
        "servertime": 1533803862
    }
}
```
### **`/finish_test`**
#### 完成试卷接口
参数：`none`  
权限：`student`  
返回示例：
```json
{
    "code": 0,
    "message": "已完成考试",
    "data": []
}
```
### **`/change_test`**
#### 更换试卷接口
参数：`none`  
权限：`student`  
返回示例：
```json
{
    "code": 0,
    "message": "更换试卷成功",
    "data": []
}
```
### **`/statistics`**
#### 查询统计成绩接口
参数：`none`  
权限：`teacher`  
返回示例：
```json
{
    "code": 0,
    "message": "查询成功",
    "data": [
        {
            "name": "建筑学院",
            "allstudents": 68,
            "finished": 2,
            "averagescore": 2.5,
            "averagetime": 231,
            "maxscore": 3,
            "minscore": 2,
            "maxtime": 350,
            "mintime": 112
        },
        {
            "name": "计算机学院",
            "allstudents": 95,
            "finished": 0
        },
        {
            "name": "软件学院",
            "allstudents": 107,
            "finished": 0
        },
        {
            "name": "all",
            "allstudents": 270,
            "finished": 2,
            "maxscore": 3,
            "minscore": 2,
            "maxtime": 350,
            "mintime": 112,
            "averagescore": 2.5,
            "averagetime": 231
        }
    ]
}
```
### **`/score_list`**
#### 查询本院成绩接口
参数：`none`  
权限：`teacher`  
返回示例：
```json
{
    "code": 0,
    "message": "查询成功",
    "data": [
        {
            "id": "01A17101",
            "name": "楼琦珍",
            "score": 3,
            "time": 112
        },
        {
            "id": "01A17102",
            "name": "卿婷玉",
            "score": 2,
            "time": 350
        },
        {
            "id": "01A17103",
            "name": "义孤容"
        },
        {
            "id": "01A17104",
            "name": "少子爱"
        },
	[...]
    ]
}
```
### **`/get_appeals`**
#### 查询待处理申诉接口
参数：`none`  
权限：`admin`  
返回示例：
```json
{
    "code": 0,
    "message": "请求成功",
    "data": [
        {
            "id": "323313",
            "account": "01A17101",
            "message": "申诉的具体内容",
            "extra": "账号存在，记录：\n2018-08-09 17:02:49 : Login failed\n2018-08-09 17:02:52 : Login failed\n2018-08-09 17:04:02 : Started an appeal\n",
            "logs": [
                "2018-08-09 17:04:02 : Appeal created"
            ]
        },
        {
            "id": "483927",
            "account": "01A17401",
            "message": "申诉的具体内容",
            "extra": "账号不存在",
            "logs": [
                "2018-08-09 17:04:31 : Appeal created"
            ]
        }
    ]
}
```
### **`/deal_appeal`**
#### 处理申诉接口
参数：
- `number` 申诉号
  
权限：`admin`  
返回示例：
```json
{
    "code": 0,
    "message": "请求成功",
    "data": []
}
```
### **`/dealing`**
#### 查询正在处理申诉接口
参数：`none`  
权限：`admin`  
返回示例：
```json
{
    "code": 0,
    "message": "请求成功",
    "data": {
        "id": "323313",
        "account": "01A17101",
        "message": "申诉的具体内容",
        "extra": "账号存在，记录：\n2018-08-09 17:02:49 : Login failed\n2018-08-09 17:02:52 : Login failed\n2018-08-09 17:04:02 : Started an appeal\n",
        "logs": [
            "2018-08-09 17:04:02 : Appeal created",
            "2018-08-09 17:06:52 : Started dealing. Admin 101"
        ]
    }
}
```
### **`/finish_appeal`**
#### 完成处理申诉接口
参数：
- `result` 处理结果
  
权限：`admin`  
返回示例：
```json
{
    "code": 0,
    "message": "请求成功",
    "data": []
}
```
### **`/change_password`**
#### 管理员改密码接口
参数：
- `newpassword` 新密码
  
权限：`admin`  
返回示例：
```json
{
    "code": 0,
    "message": "修改密码成功",
    "data": []
}
```
### **`/create_account`**
#### 完成处理申诉接口
参数：
- `name` 名字
- `newpassword` 新密码
  
权限：`admin`  
返回示例：
```json
{
    "code": 0,
    "message": "创建账号成功",
    "data": []
}
```
### **`/finish_appeal`**
#### 完成处理申诉接口
参数：
- `newpassword` 新密码
  
权限：`admin`  
返回示例：
```json
{
    "code": 0,
    "message": "请求成功",
    "data": []
}
```
