The Update\Manual folder is for store the manual update classes of new version of code to be run.


File name guideline:
The file name **should be** named by refer to plugin version and sub actions of manual update to be run.
The file name will be load order by natural sort ascending (1, 2, 3, ..., 10, 11, ..., 101, 102 and so on).
The sub folder is allowed but the namespace must follow PSR auto load.
For example: plugin version is 1.0 and this update have many code to run, it will split to 3 actions then it will be...
* V1Sub1.php
* V1Sub2.php
* V1Sub3.php
Or you can use sub folder:
* V1\UpdateSub1.php


Update class structure guideline:
The class name must follow PSR auto load.
The update class MUST have `manual_update_version` property for check later that what manual update version had already run.
The `manual_update_version` in each update class can be the same for one update.
The update class may have `__construct()` method but must has nothing working from there. This constructor method will be loaded while checking for having update class or not and use `manual_update_version` property to compare.
The update class MUST have `run()` method to run the update code.
Example:
```
<?php
namespace RundizableWpFeatures\App\Update\Manual;

class V1Sub1 implements \RundizableWpFeatures\App\Update\Manual\ManualUpdateInterface
{


    public $manual_update_version = '0.1';


    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // Code your manual update for action 1 (sub1).
    }// run


}
```
