<?php
class PHPGraphics {
    /* Function:A class that can be used to make charts
     * Date:2019-07-15
     * Author:CaoAng
     * Version:pre-190715
     * Describe:The version is not stable,and it doesn't support using Chinese!
     */
    public $gdres;
    public $data;
    public $colors=array(
        0=>0xff0000,
        1=>0x0000ff,
        2=>0x00ff00,
        3=>0xffff00,
        4=>0x00ffff,
        5=>0xff00ff,
        6=>0xc0c0c0
    );
    function __construct(){
        
    }
    function __destruct(){
        
    }
    function createImage($x,$y,$from=0,$filename=null){
        switch ($from){
            case 0:
                $this->gdres=imagecreatetruecolor($x,$y);
                imagefill($this->gdres,0,0,0xc0c0c0);
            case 1:
                //$this->gdres=imagecreatefromgif($filename);
            case 2:
                //$this->gdres= imagecreatefromjpeg($filename);
            case 3:
                //$this->gdres= imagecreatefrompng($filename);
        }
    }
    function barChart($data,$x,$y,$sx,$sy,$flagexam=true,$flagfill=false,$fillcolor=0){
        /*$data为二维数组，格式如下：
         * 1.每个键对应一个项目，必须是数组
         * 2.每个键下的每个键是一个数据
         */
        $max=$this->maxOfData($data); 
        $count=count($data,1);
        $barwide=round(($sx*0.8)/$count);
        $chartsy=$sy;
        //$itemwide=round(($sy*0.85)/($items+5));
        if($flagfill){
            imagefilledrectangle($this->gdres,$x,$y,$x+$sx,$y+$sy,$fillcolor);
        }
        if($flagexam){
            $this->example($data,$x,$y,$sx,$sy);
            $chartsy=round($sy*0.7);
        }
        imageline($this->gdres,$x+0.15*$sx,$y+$chartsy-16,$x+$sx,$y+$chartsy-16,0);
        imageline($this->gdres,$x+0.15*$sx,$y+$chartsy-16,$x+0.15*$sx,$y,0);
        for($i=0;$i<=10;$i++){
            $num=$max*($i/10);
            $numy=round($y+$chartsy*((11-$i)/11))-23;
            imagestring($this->gdres,3,$x,$numy,$num,0);
        }
        $curritem=0;
        foreach($data as $item){
            $currdat=0;
            foreach($item as $val){
                $barx=round($x+0.15*$sx+$barwide*count($item)*$currdat+$barwide*$curritem)+1;
                $barhigh=round(($chartsy-1)*($val/$max));
                //echo $barx.'/';
                imagefilledrectangle($this->gdres,$barx,$y+$chartsy-17,$barx+$barwide,$y+$chartsy-17-$barhigh,$this->colors[$curritem]);
                $currdat++;
            }
            $curritem++;
        }
        //echo $itemwide;
    }
    function lineChart($data,$x,$y,$sx,$sy,$flagexam=true,$flagfill=false,$fillcolor=0){
        $max=$this->maxOfData($data); 
        $count=count($data,1)/count($data);
        $wide=round(($sx*0.8)/($count-2));
        $chartsy=$sy;
        //$itemwide=round(($sy*0.85)/($items+5));
        if($flagfill){
            imagefilledrectangle($this->gdres,$x,$y,$x+$sx,$y+$sy,$fillcolor);
        }
        if($flagexam){
            $this->example($data,$x,$y,$sx,$sy);
            $chartsy=round($sy*0.7);
        }
        imageline($this->gdres,$x+0.15*$sx,$y+$chartsy-16,$x+$sx,$y+$chartsy-16,0);
        imageline($this->gdres,$x+0.15*$sx,$y+$chartsy-16,$x+0.15*$sx,$y,0);
        for($i=0;$i<=10;$i++){
            $num=$max*($i/10);
            $numy=round($y+$chartsy*((11-$i)/11))-23;
            imagestring($this->gdres,3,$x,$numy,$num,0);
        }
        $curritem=0;
        foreach($data as $item){
            $currdat=0;
            $last=$y+$chartsy-17-round(($chartsy-1)*(current($item)/$max));
            foreach($item as $val){
                $linex=round($x+0.15*$sx+$wide*($currdat-1))+1;
                $high=round(($chartsy-1)*($val/$max));
                //echo $barx.'/';
                if($currdat!=0){
                    imageline($this->gdres,$linex,$last-1,$linex+$wide,$y+$chartsy-18-$high,$this->colors[$curritem]);
                    imageline($this->gdres,$linex,$last,$linex+$wide,$y+$chartsy-17-$high,$this->colors[$curritem]);
                    imageline($this->gdres,$linex,$last+1,$linex+$wide,$y+$chartsy-16-$high,$this->colors[$curritem]);
                }
                $currdat++;
                $last=$y+$chartsy-17-$high;
            }
            $curritem++;
        }
    }
    function pieChart($data,$x,$y,$sx,$sy,$flag3d=true,$flagexam=true,$flagpercent=true,$flagfill=false,$fillcolor=0,$center=null){
        $total=array_sum($data);
        //echo $total.'#';
        $chartsy=$sy;
        $chartsx=round(0.8*$sx);
        if($flagfill){
            imagefilledrectangle($this->gdres,$x,$y,$x+$sx,$y+$sy,$fillcolor);
        }
        if($flagexam){
            $examy=round($y+$sy*0.7);
            $examx=$x;
            $curritem=0;
            foreach($data as $key=>$val){
                if($examx+18+9*strlen($key)+45>$sy){
                    $examy+=18;
                    $examx=$x;
                }
                imagefilledrectangle($this->gdres,$examx,$examy,$examx+15,$examy+15,$this->colors[$curritem]);
                $examx+=18;
                imagestring($this->gdres,3,$examx,$examy,$key,0);
                $examx+=9*strlen($key);
                if($flagpercent){
                    imagestring($this->gdres,3,$examx,$examy,round($val/$total*100,1).'%',0);
                    $examx+=45;
                }
                $curritem++;
            }
            $chartsy=round($sy*0.5);
        }
        if($center==null){
            $center=array(round($sx/2),round(1.5*$chartsy/2));
        }
        if($flag3d==true){
            //$piery=round(0.3*$chartsx);
            $floors=round(0.1*$chartsx);
            $center[1]+=round($floors/2);
        }else{
            $piery=$r;
            $floors=1;
        }
        for($i=0;$i<$floors-1;$i++){
            $angle_passed=0;
            $curritem=0;
            foreach($data as $val){
                $angle=$val/$total*360;
                imagefilledarc($this->gdres,$center[0],$center[1]-$i,$chartsx,$chartsy,$angle_passed,$angle_passed+$angle,$this->colors[$curritem]&0xe0e0e0,IMG_ARC_PIE);
                $curritem++;
                //echo $angle_passed.'/';
                $angle_passed+=$angle;
            }
        }
        $angle_passed=0;
        $curritem=0;
        //print_r($data);
        //$test_x=150;
        //$center[0]=$test_x;
        foreach($data as $val){
            //echo $val;
            //print_r($data);
            $angle=$val/$total*360;
            //echo $angle_passed.'/'.$angle.'|';
            imagefilledarc($this->gdres,$center[0],$center[1]-$floors,$chartsx,$chartsy,$angle_passed,$angle_passed+$angle,$this->colors[$curritem],IMG_ARC_PIE);
            $curritem++;
            $angle_passed+=$angle;
            //$center[0]+=250;
        }
    }
    function example($data,$x,$y,$sx,$sy){
        $examy=round($y+$sy*0.7);
        $examx=$x;
        $curritem=0;
        foreach($data as $key=>$val){
            if($examx+18+9*strlen($key)>$sy){
                $examy+=18;
                $examx=$x;
            }
            imagefilledrectangle($this->gdres,$examx,$examy+20,$examx+15,$examy+15+20,$this->colors[$curritem]);
            $examx+=18;
            imagestring($this->gdres,3,$examx,$examy+20, $key,0);
            $examx+=9*strlen($key);
            $curritem++;
        }
        $curritem=0;
        foreach($val as $k=>$v){
            imagestring($this->gdres,3,round($x+0.15*$sx+($sx*0.85)*($curritem+0.3)/count($val)),$examy-16,$k,0);
            $curritem++;
        }
    }
    function maxOfData($data){
        $max=0;
        foreach($data as $item){
            foreach($item as $val){
                if($val>=$max){
                    $max=$val;
                }
            }
        }
        if($max<=0.2*pow(10,ceil(log10($max)))){
            $max=0.2*pow(10,ceil(log10($max)));
        }else if($max<=0.3*pow(10,ceil(log10($max)))&&$max>0.2*pow(10,ceil(log10($max)))){
            $max=0.3*pow(10,ceil(log10($max)));
        }else if($max<=0.5*pow(10,ceil(log10($max)))&&$max>0.3*pow(10,ceil(log10($max)))){
            $max=0.5*pow(10,ceil(log10($max)));
        }else{
            $max=pow(10,ceil(log10($max)));
        }
        return $max;
    }
}
/*$g=new PHPGraphics;
$g->createImage(400,300,0);
$g->barChart(array('Xu Yanan'=>array('mon'=>827,'tue'=>2005,'wed'=>2660,'thu'=>520,'fri'=>1666),'Test A'=>array('mon'=>1666,'tue'=>2345,'wed'=>2827,'thu'=>1314,'fri'=>99),'Test B'=>array('mon'=>1827,'tue'=>1679,'wed'=>999,'thu'=>2520,'fri'=>1440),'20050827'=>array('mon'=>527,'tue'=>2587,'wed'=>2018,'thu'=>1840,'fri'=>1234)),25, 20, 400,300);
//$g->pieChart(array('mon'=>827,'tue'=>2019,'wed'=>2660,'thu'=>520,'fri'=>1666),0,0,400,300);
imagepng($g->gdres,'bar.png');*/
?>
