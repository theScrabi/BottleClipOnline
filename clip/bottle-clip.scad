name = "ymk";
bottle_type = "din-6075"; // [din-6075:"DIN 6075 (Club Mate, most beers)", din-6198:"DIN 6198 (Euro2)", din-6199:"DIN 6199 (Steinie)", longneck:"Longneck (Fritz Cola)", custom:Custom]
logo = "icons/pesthoernchen.dxf";
name_font = "write_lib/orbitron.dxf";
// thickness of the clip
width=2;
// the back in the back of the clip
gap=90;
// only applies with bottle_type=custom
upper_radius=13;
// only applies with bottle_type=custom
lower_radius=22.5;
// only applies with bottle_type=custom
height=26;


//include <write_lib/Write.scad>

/**
 * Creates one instance of a bottle clip name tag. The default values are
 * suitable for 0.5l Club Mate bottles (and similar bottles). By default, logo
 * and text are placed on the name tag so they both share half the height. If
 * there is no logo, the text uses the total height instead.
 * Parameters:
 * ru: the radius on the upper side of the clip
 * rl: the radius on the lower side of the clip
 * ht: the height of the clip
 * width: the thickness of the wall. Values near 2.5 usually result in a good
 *	clippiness for PLA prints.
 * name: the name that is printed on your name tag. For the default ru/rt/ht
 *	values, this string should not exceed 18 characters to fit on the name tag.
 * gap: width of the opening gap, in degrees. For rigid materials this value
 *  usually needs to be near 180 (but if you set it to >= 180, you won't have
 *  anything left for holding the clip on the bottle). For flexible materials
 *  like Ninjaflex, choose something near 0. For springy materials like PLA or
 *  ABS, 90 has proven to be a good value.
 * logo: the path to a DXF file representing a logo that should be put above
 *	the name. Logo files should be no larger than 50 units in height and should
 *	be centered on the point (25,25). Also all units in the DXF file should be
 *	in mm. This parameter can be empty; in this case, the text uses the total
 *	height of the name tag.
 * font: the path to a font for Write.scad.
 */
module bottle_clip(ru=13, rl=17.5, ht=26, width=2.5, name="", gap=90,
		logo="thing-logos/stratum0-lowres.dxf", font="orbitron.dxf") {
    
	e=100;  // should be big enough, used for the outer boundary of the text/logo

	difference() {
		rotate([0,0,-45]) union() {
			// main cylinder
			cylinder(r1=rl+width, r2=ru+width, h=ht);
			// text and logo
			if(logo == "") {
				//writecylinder(name, [0,0,3], rl+0.5, ht/13*7, h=ht/13*8, t=max(rl,ru),
				//	font=font);
			} else {
				//writecylinder(name, [0,0,0], rl+0.5, ht/13*7, h=ht/13*4, t=max(rl,ru),
				//	font=font);
				/*
				translate([0,0,ht*3/4-0.1])
					rotate([90,0,0])
					scale([ht/100,ht/100,1])
					translate([-25,-25,0.5])
					linear_extrude(height=max(ru,rl)*2)
					import(logo);
					*/
			}
		}
		// inner cylinder which is substracted
		translate([0,0,-1])
			cylinder(r1=rl, r2=ru, h=ht+2);
		// outer cylinder which is substracted, so the text and the logo end
		// somewhere on the outside ;-)
		difference () {
			cylinder(r1=rl+e, r2=ru+e, h=ht);
			translate([0,0,-1])
				// Note: bottom edges of characters are hard to print when character
				// depth is > 0.7
				cylinder(r1=rl+width+0.7, r2=ru+width+0.7, h=ht+2);
		}

		// finally, substract an equilateral triangle as a gap so we can clip it to
		// the bottle
		gap_x=50*sin(45-gap/2);
		gap_y=50*cos(45-gap/2);
		translate([0,0,-1])
			linear_extrude(height=50)
			polygon(points=[[0,0], [gap_x, gap_y], [50,50], [gap_y, gap_x]]);
	}
}


if(bottle_type == "din-6198"){
    bottle_clip(ru=13, rl=22.5, ht=26, width=width, name=name, gap=gap, logo=logo, font=name_font);
}
if(bottle_type == "din-6075"){
    bottle_clip(ru=13, rl=17.5, ht=26, width=width, name=name, gap=gap, logo=logo, font=name_font);
}
else if(bottle_type == "din-6199"){
    bottle_clip(ru=13, rl=17.5, ht=13, width=width, name=name, gap=gap, logo=logo, font=name_font);
}
else if(bottle_type == "longneck"){
    bottle_clip(ru=13, rl=15, ht=26, width=width, name=name, gap=gap, logo=logo, font=name_font);
}
else {
    bottle_clip(ru=upper_radius, rl=lower_radius, ht=height, width=width, name=name, gap=gap, logo=logo, font=name_font);
}