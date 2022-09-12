### 基于Lumen6.0以上，自动生成CURD（含service）
```
- install: composer require stru/lumen-generator
- 说明：基于lumen6.0以上开发的根据表名自动生成完整CURD，包括Service
- 注意：
    - 1、安装包后要将本包的config目录下的配置文件复制到项目config下
    - 2、安装包后要将本包的routes目录下的路由文件复制到项目routes
    - 3、控制器、Service、模型都有基类模型，Base[Controller | Service | Model],请自行创建
    - 4、模板不具有通用性，可以根据自己需求更改。

- 自动生成CURD带二级的admin模块 php artisan stru:admin Set(目录名)/Company(控制器文件名) --fromTable --tableName=set_company(表名)
- 自动生成CURD的Admin模块 php artisan stru:admin Company(控制器文件名) --fromTable --tableName=set_company

- 自动生成CURD带二级的api模块 php artisan stru:api Set(目录名)/Company(控制器文件名) --fromTable --tableName=set_company(表名)
- 自动生成CURD的api模块  php artisan stru:api Company(控制器文件名) --fromTable --tableName=set_company

- 自动生成模型带二级 php artisan stru:model Set(目录名)/Company(模型文件名) --fromTable --tableName=set_company(表名)
- 自动生成模型 php artisan stru:model Company(模型文件名) --fromTable --tableName=set_company
```