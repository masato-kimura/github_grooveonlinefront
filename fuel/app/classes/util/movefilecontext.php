<?php
namespace util;

class MoveFileContext {

    private $moveFileConcreteStrategy;

    public function __construct(MoveFileStrategy $moveFileConcreteStrategy) {
        $this->moveFileConcreteStrategy = $moveFileConcreteStrategy;
    }

    /*
     * @return boolen
    */
    public function chkdir($path) {
        return $this->moveFileConcreteStrategy->chkdir($path);
    }

    /*
     * @return dirName or false
    */
    public function mkdir($dirName) {
        return $this->moveFileConcreteStrategy->mkdir($dirName);
    }

    /*
     * @return true or Exception
    */
    public function del($path) {
        return $this->moveFileConcreteStrategy->del($path);
    }

    /*
     * @return true or Exception
     */
	public function upload($fromPath, $toPath, $type=null)
	{
		return $this->moveFileConcreteStrategy->upload($fromPath, $toPath, $type);
    }

}