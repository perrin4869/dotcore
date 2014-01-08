<?

require_once("class_profiling.inc.php");

$profil=new profiling();

function work() { // a function which simulate a working code by usleep ...
	usleep(rand(1,500000)/10);
}

function foo() {
	global $profil;
	$profil->increase();
	$profil->add("beginning of function foo");
	work(); // some stuff
	$profil->add("end of function foo");
	$profil->decrease();
	return;
}

$profil->add("beginning of main");

work(); // some stuff

for ($i=0;$i<10;$i++) {
	$profil->add("loop #$i");
	foo();
	work(); // some stuff
}

$profil->end();
echo $profil->get_result();

?>
