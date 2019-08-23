<?php
class PHPGraphicsString{
    /* Function:A class that can write some words on a picture,using a set of dot martix font.
     * Date:2019-07-16
	 * Version:pre-190823
     * Author:LoveXYN0827
     */
    public $gdres;
    public $string;
    public $font;
    public $font_sx;
    public $font_sy;
    public function __construct($font=null,$font_sx=null,$font_sy=null,$string=null){
        if($font!=null){
            $this->font=fopen($font,'rb');
            $this->font_sx=$font_sx;
            $this->font_sy=$font_sy;
        }
        if(!isset($string)){
            $this->string=$string;
        }
    }
    public function __destruct(){
        if($this->font!=null){
            fclose($this->font);
        }
    }
    public function loadFont($font,$sx,$sy){
        $this->font_sx=$sx;
        $this->font_sy=$sy;
        $this->font=fopen($font,"rb");
    }
    public function writeChar($char,$x,$y,$sx,$sy,$color=0){
        if(strlen($char)==2){
            $char_size=round($this->font_sx*$this->font_sx/8);
            if($this->font_sx>=24){
                $offset=$char_size*(94*(ord($char[0])-0xa1-0xf)+ord($char[1])-0xa1);
            }else{
                $offset=$char_size*(94*(ord($char[0])-0xa1)+ord($char[1])-0xa1);
            }
            fseek($this->font,$offset);
            $dots_bin=fread($this->font,$char_size);
            $font_sx=$this->font_sx;
            $font_sy=$this->font_sy;
        }else if(strlen($char)==1){
            $char_size=$this->font_sx*$this->font_sy/8;
            $offset=$char_size*(ord($char)+156-1);
            fseek($this->font,$offset);
            $dots_bin=fread($this->font,$char_size);
            /*echo $char_size;
            echo 0;*/
            $font_sx=$this->font_sx;
            $font_sy=$this->font_sy;
        }
        //echo $char_size."/";
        $dots_num="";
        //echo strlen($dots_bin).'-'.$char_size.'-'.$offset.'/';
        for($i=0;$i<$char_size;$i++){
            $dots_num.=sprintf("%08b",ord($dots_bin[$i]));
        }
        $count=0;
        for($j=0;$j<$font_sx;$j++){
            //echo $i.'/';
            for($i=0;$i<$font_sy;$i++){
                //echo $j;
                //echo $i.'/'.$j;
                if($dots_num[$count]==1){
                    $zoom_x=$sx/$font_sx;
                    $zoom_y=$sy/$font_sy;
                    imagefilledrectangle($this->gdres,$x+round($i*($zoom_x)),$y+round($j*($zoom_y)),$x+round($i*($zoom_x))+round(($zoom_x)-1),$y+round($j*($zoom_y))+round(($zoom_y)-1),$color);
                }
                $count++;
            }
        }
    }
    public function writeString($string,$x,$y,$char_sx,$char_sy,$color=0){
        if(ord($string[0])>=0xa0){
            $x-=$char_sx;
        }else{
            $x-=$char_sx;
        }
        $i=0;
        while($i<strlen($string)){
            if(ord($string[$i])>=0xa0){
                $char=substr($string,$i,2);
                $i+=2;
                $char_sx=$char_sx;
                $char_sy=$char_sy;
            }else{
                $char=$string[$i];
                $i+=1;
                $char_sx=$char_sx;
                $char_sy=$char_sy;
            }
            $x+=$char_sx;
            $this->writeChar($char,$x,$y,$char_sx,$char_sy,$color);
            //echo $char.'/';
            //echo 0;
            //sleep(2);
        }
    }
}
/*$g=new PHPGraphicsString("hzk16",16,16,"ASC16",8,16);
$g->gdres=imagecreatetruecolor(4000,300);
imagefill($g->gdres,0,0,0xffffff);
$g->writeString("ÖÐÎÄ English",0,0,160,160);
//echo $g->font_sx_asc;
imagepng($g->gdres,"xyn.png");*/
?>