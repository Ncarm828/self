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
