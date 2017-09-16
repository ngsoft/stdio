<?php

namespace NGSOFT;

/**
 * Simple PHP Cli STDIO Class
 * Gives The ability to colorize output
 * and to get shell input
 * @author Aymeric Anger <daedelus.git@gmail.com>
 * @link https://github.com/NGSOFT Github Repository
 */
class STDIO {
    
    
    const VERSION = '0.9.9';

    
    /*
     * Foreground and Background colors
     */
    const black = 'black';
    const dark_gray = 'dark_gray';
    const blue = 'blue';
    const light_blue = 'light_blue';
    const green = 'green';
    const light_green = 'light_green';
    const cyan = 'cyan';
    const light_cyan = 'light_cyan';
    const red = 'red';
    const light_red = 'light_red';
    const purple = 'purple';
    const light_purple = 'light_purple';
    const brown = 'brown';
    const yellow = 'yellow';
    const light_gray = 'light_gray';
    const white = 'white';
    const magenta = 'magenta';
    const light_yellow = 'light_yellow';
    const light_magenta = 'light_magenta';

    /*
     * Styles
     */
    const s_reset            = 'reset';
    const s_bold             = 'bold';
    const s_dark             = 'dark';
    const s_italic           = 'italic';
    const s_underline        = 'underline';
    const s_blink            = 'blink';
    const s_reverse          = 'reverse';
    const s_concealed        = 'concealed';
    
    
    
    protected $background = [
        'default'       => 49,
        'black'         => 40,
        'red'           => 41,
        'green'         => 42,
        'yellow'        => 43,
        'blue'          => 44,
        'magenta'       => 45,
        'cyan'          => 46,
        'light_gray'    => 47,
        
        'dark_gray'     => 100,
        'light_red'     => 101,
        'light_green'   => 102,
        'light_yellow'  => 103,
        'light_blue'    => 104,
        'light_magenta' => 105,
        'light_cyan'    => 106,
        'white'         => 107
    ];
    
    
    protected $colors = [
        'default'          => 39,
        'black'            => 30,
        'red'              => 31,
        'green'            => 32,
        'yellow'           => 33,
        'blue'             => 34,
        'magenta'          => 35,
        'cyan'             => 36,
        'light_gray'       => 37,
        
        'dark_gray'        => 90,
        'light_red'        => 91,
        'light_green'      => 92,
        'light_yellow'     => 93,
        'light_blue'       => 94,
        'light_magenta'    => 95,
        'light_cyan'       => 96,
        'white'            => 97
    ];
    
    protected $style = [
        'reset'            => 0,
        'bold'             => 1,
        'dark'             => 2,
        'italic'           => 3,
        'underline'        => 4,
        'blink'            => 5,
        'reverse'          => 7,
        'concealed'        => 8
    ];
    
    /*
     * Default values for a terminal
     */
    protected $terminal = [
        'cols'      =>      80,
        'lines'     =>      24,
        'term'      =>      false
    ];


    public function __construct() {
        if(PHP_SAPI != 'cli' and !empty($_SERVER['DOCUMENT_ROOT'])){
            throw  new Exception('Not in a shell environnement : Cannot execute');
            exit(1);
        }
        $this->getEnv();
        
    }
    
    protected function getEnv(){
        //is a job or not in a terminal
        if(!isset($_SERVER['TERM'])) return;
        //is under windows so no tput
        if(preg_match('/^[a-zA-Z]:/', __DIR__)) return;
        $this->terminal['term']=true;
        foreach ($this->terminal as $key => &$val){
            if($key == 'term') continue;
            $val = (int)  exec(sprintf('tput %s',$key));
        }
    }
    
    /**
     * Gives the given string a style
     * @param string $text
     * @param string $color can use declared constants
     * @param string $backcolor can use declared constants
     * @param string $style can use declared 's_' constants
     * @return colored string
     * @throws Exception
     */
    public function getString($text, $color = null, $backcolor = null, $style = null){
        $out = '';
        if($color){
            if(!isset($this->colors[$color])) throw new Exception (sprintf ('%s : Color %s is invalid',  get_called_class (),$color));
            $color = $this->colors[$color];
            
            if($color >= 90) $color = [1,($color - 90 +30)];
            else $color = [0,$color];
            if($style){
                if(!isset($this->style[$style])) throw new Exception (sprintf ('%s : Style %s is invalid',  get_called_class (),$style));
                $color[0] = $this->style[$style];
            }
            
            $out.=sprintf("\033[%sm", implode(';', $color));
        }
        if($backcolor){
            if(!isset($this->background[$backcolor])) throw new Exception (sprintf ('%s : Background color %s is invalid',  get_called_class (),$backcolor));
            $out.=sprintf("\033[%dm",  $this->background[$backcolor]);
        }
        $out.=$text;
        $out.="\033[0m";
        return $out;
    }
    
    /**
     * Get STDIN
     * @param type $default default value returned on enter
     * @return string
     */
    public function in($default = null){
        $input = fgets(STDIN);
        //remove \n
        $input = str_replace(PHP_EOL, '', $input);
        if(empty($input)) $input = $default;
        return $input;
    }
    
    


    /**
     * Print STDOUT
     * @param type $text
     * @param type $color
     * @param type $backcolor
     * @param type $style
     * @return \CLI\STDIO
     */
    public function out($text = null, $color = null, $backcolor = null, $style = null){
        if(is_null($text)){
            extract($this->line);
            if($prefix) $this->out($prefix);
        }
        
        fwrite(STDOUT, $this->getString($text, $color, $backcolor, $style));
        return $this;
    }
    
    /**
     * Print STDERR
     * @param type $text
     * @param type $color
     * @param type $backcolor
     * @param type $style
     * @return \CLI\STDIO
     */
    public function err($text = null, $color = null, $backcolor = null, $style = null){
        if(is_null($text)){
            extract($this->line);
            if($prefix) $this->out($prefix);
        }
        fwrite(STDERR, $this->getString($text, $color, $backcolor, $style));
        return $this;
    }
    
    /**
     * End the script
     * @param type $status
     */
    public function stop($status = 0){
        exit((int)$status);
    }
    
    //================================== Zen Coding Mode ==================================//
    
    protected $line = [
        'style'     =>  null,
        'color'     =>  null,
        'backcolor' =>  null,
        'text'      =>  null,
        'prefix'    =>  null
    ];
    
    public function __call($name, $arguments) {
        if(in_array($name, array_keys($this->style))){
            $this->line['style'] = $name;
            return $this;
        }
        if(in_array($name, array_keys($this->colors))){
            $this->line['color'] = $name;
            return $this;
        }
        $pos = strpos($name, 'bg_');
        if(empty($pos) and !is_bool($pos)){
            $name = substr($name, 3);
            if(in_array($name, array_keys($this->background))){
                $this->line['backcolor'] = $name;
                return $this;
            }
            
        }
        if(method_exists($this, $name)) return $this->$name();
        return $this;
    }

    public function __get($name) {
        return $this->__call($name,[]);
    }

    public function __invoke($text) {
        return $this->text($text);
    }
    
    /**
     * Set the text to display
     * @param string $text
     * @return \CLI\STDIO
     */
    public function text($text = null){
        foreach ($this->line as &$val){
            $val = null;
        }
        $this->line['text'] = $text;
        return $this;
    }
    
    /**
     * Center the output or pad to the given value
     * @param int $padding Add left padding
     * @return \CLI\STDIO
     */
    public function center($padding = null){
        if(!is_int($padding) or $padding < 1){
            $length = strlen($this->line['text']);
            $padding = $this->terminal['cols'] - $length;
            $padding = $padding/2;
            $padding = floor($padding);
        }
        //legacy mode
        if(!$this->terminal['term']){
            $this->line['prefix'] = "\r".str_pad('', $padding);
        }
        //normal mode
        else $this->line['prefix']=  sprintf("\r\033[%dC",$padding);
        return $this;
    }
    
    /**
     * Align the output to the right
     * @param int $padding Add Right Padding
     */
    public function right($padding = 1){
        if(!is_int($padding) or $padding < 1) $padding = 1;
        $length = strlen($this->line['text']);
        $pad = $this->terminal['cols'] - $length - $padding;
        return $this->center($pad);
    }


    /**
     * Add a separation line
     * @param string $repeat
     * @return \CLI\STDIO
     */
    public function separator($repeat = '='){
        //$text=PHP_EOL;
        $text=str_pad('', $this->terminal['cols'],$repeat[0]);
        $text.=PHP_EOL;
        return $this->text($text);
        
    }
    
    /**
     * Add a line Break
     * @return $this
     */
    public function eol(){
        $this->line['text'].=PHP_EOL;
        return $this;
    }
    
    /**
     * Returns the pointer to the beginning of the line
     * @return $this
     */
    public function bol(){
        $this->line['text'].="\r";
        return $this;
    }
    
    public function __set($name, $value) {}

    /**
     * Use with print
     * @return string
     */
    public function __toString() {
        extract($this->line);
        $string = $this->getString($text, $color, $backcolor, $style);
        if($prefix) $string = $prefix.$string;
        return $string;
    }
    
    /**
     * Clear the screen
     * /!\ do not use print/->out
     * Creates lines to not erase previous terminal content
     * and clear them
     * @return $this
     */
    public function cls(){
        //return $this->out("\033[2J");
        for ($i=0; $i < $this->terminal['lines']+1;$i++){
            $this->out(PHP_EOL);
        }
        return $this->clearline($i);
    }
    /**
     * Clears lines
     * /!\ do not use print/->out
     * @param int $depth Number of lines
     * @return $this
     */
    public function clearline($depth = 0){
        if(!is_int($depth)) $depth = 0;
        if($depth == 0) return $this->out("\r\033[K");
        for($i=0; $i<$depth;$i++){
            if(empty($i)) $this->out("\r\033[K");
            $this->out("\033[1A\r\033[K\r");
        }
        return $this;
    }
    
    /**
     * Move Cursor Up
     * /!\ do not use print/->out
     * @param int $depth Number of lines
     * @return $this
     */
    public function MoveUp($depth = 1){
        if(!is_int($depth) or $depth < 1) return $this;
        return $this->out(sprintf("\033[%dA\r",$depth));
    }
    
    /**
     * Get Cursor Down
     * /!\ do not use print/->out
     * @param int $depth Number of lines
     * @return $this
     */
    public function MoveDown($depth = 1){
        if(!is_int($depth) or $depth < 1) return $this;
        return $this->out(sprintf("\033[%dB\r",$depth));
    }
    
    /**
     * Move Cursor Left
     * /!\ do not use print/->out
     * @param int $depth Number of lines
     * @return $this
     */
    public function MoveLeft($depth = 1){
        if(!is_int($depth) or $depth < 1) return $this;
        return $this->out(sprintf("\r\033[%dD",$depth));
    }
    
    /**
     * Move Cursor Right
     * /!\ do not use print/->out
     * @param int $depth Number of lines
     * @return $this
     */
    public function MoveRight($depth = 1){
        if(!is_int($depth) or $depth < 1) return $this;
        return $this->out(sprintf("\r\033[%dC",$depth));
    }
    
    
    
}
