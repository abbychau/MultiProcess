<?php

namespace Fizzday\MultiProcess;

abstract class MultiProcess
{
    /**
     * 数据总数量
     * @var int
     */
    public $pcntl_total;

    /**
     * 每一次发送的数据总数量
     * @var int
     */
    public $pcntl_perTotalNum;

    /**
     * 计划使用总进程数
     * @var int
     */
    public $pcntl_num;

    /**
     * 初始化
     * MultiProcess constructor.
     * @param int $pcntl_total
     * @param int $pcntl_perTotalNum
     * @param int $pcntl_num
     */
    public function __construct($pcntl_total = 1000000, $pcntl_perTotalNum = 10000, $pcntl_num = 100)
    {
        $this->pcntl_total = $pcntl_total;
        $this->pcntl_perTotalNum = $pcntl_perTotalNum;
        $this->pcntl_num = $pcntl_num;
    }

    /**
     * 多进程任务开始调用方法
     */
    public function pcntlAct()
    {
        // 需要循环的次数
        $pcntl_actNum = ceil($this->pcntl_total / $this->pcntl_perTotalNum) ;	// 需要发送的次数

        for ($i=0; $i < $pcntl_actNum; $i++) {

            // 定义记录子进程的数组
            $pids = array();

            for ($j=0; $j < $this->pcntl_num; $j++) {

                $pids[$j] = pcntl_fork();

                switch ($pids[$j]) {
                    case -1:
                        echo "开启子进程失败 : {$j} \r\n";
                        exit;

                    case 0:
                        // 开启子进程成功, 做相应操作
                        $this->pcntlTrue($j, $i);
                        exit;

                    default:
                        break;
                }

            }

            // 等待子进程退出并回收
            foreach ($pids as $k => $pid) {
                if($pid) {
                    pcntl_waitpid($pid, $status);
                }
            }

        }
    }

    /**
     * 集成后重写的具体操作
     * @param $i num 第几次循环
     * @param $j num 本次循环的第几个进程
     */
    abstract public function pcntlTrue($j, $i);
}