# self


### URLs
* Get all attributes for user - json:
    * GET http://mikekorostelev.com/~bits/self/db/user/[username]   
* Register plugin:
    * POST http://mikekorostelev.com/~bits/self/db/user/[username]/plugin_register
* Update activity / initialize prompt - returns calculated participation, enagement, responsiveness values - json:
    * POST http://mikekorostelev.com/~bits/self/db/user/[username]/update?fields=api_key,activity,tp,ti
* Register
    * POST http://mikekorostelev.com/~bits/self/db/register?fields=name,email,password
* List of user names - json:
    * GET http://mikekorostelev.com/~bits/self/db/users

### Test user table
http://mikekorostelev.com/~bits/self/app/

### Android app skeleton
Basic google fit api. Requests permission to use google account, posts using above urls - steps activity for user "mike" since start of day

https://github.com/korostelevm/self_android

### Example mongo document
      {
      	"_id" : ObjectId("554a4182411aa2a252a7f8b7"),
      	"user" : "mike",
      	"email" : "korostelevm@gmail.com",
      	"password" : "$2a$10$acdd0a6498defdcb53095uDzpLvg7YgS21SHxnoaSNwBxg8p90tdm",
      	"api_key" : "73826b92bcb7d02cc9674d333ed0f791",
      	"p" : NumberLong(31530),
      	"e" : NumberLong(213),
      	"r" : NumberLong(0),
      	"ti" : "1430932374",
      	"tp" : "1430932374",
      	"contexts" : [
      		"game",
      		"calendar"
      	]
      }
