$fn = 100;

plate_thiknes = 1;
rim_height = 2;
screw_hole_width = 3;
screw_hole_distance = 84.10;
width = 98;
clip_distance = 86.5;
clip_height = 5;
clip_width = 1.5;
clip_depth = 6;
clip_socket_depth = 2.8;
latch_height = 1.5;
latch_width = 1.5;

module prism(l, w, h) {
    polyhedron(//pt 0        1        2        3        4        5
        points=[[0,0,0], [l,0,0], [l,w,0], [0,w,0], [0,w,h], [l,w,h]],
        faces=[[0,1,2,3],[5,4,3,2],[0,4,5,1],[0,3,4],[5,2,1]]);
}

module clip() {
    union() {
        translate([-(clip_depth/2), ((clip_distance)/2)-clip_width-clip_socket_depth, 0]) {
            prism(clip_depth, clip_socket_depth, clip_socket_depth);
        }
        translate([(clip_depth/2), ((clip_distance)/2)+2.8, 0]) {
            rotate([0,0,180]) {
                prism(clip_depth, clip_socket_depth, clip_socket_depth);
            }
        }
        translate([0, -((clip_distance-clip_width)/2), clip_height/2 + plate_thiknes]) {
            union() {

                cube([clip_depth, clip_width, clip_height], true);
                translate([0, latch_width, (clip_height-latch_height)/2]) {
                    cube([clip_depth, latch_width, latch_height], true);
                }
            } 
        }
    }
}

union() {
    difference() {
        cylinder(plate_thiknes + rim_height, width/2 + rim_height, width/2 + rim_height);
        translate([0, 0, plate_thiknes]) {
            cylinder(100, width/2, width/2);
        }
        translate([screw_hole_distance/2, 0, -2]) {
            cylinder(100, screw_hole_width/2,screw_hole_width/2);
        }
        translate([-screw_hole_distance/2, 0, -2]) {
            cylinder(100, screw_hole_width/2,screw_hole_width/2);
        }
    }

    clip();
    rotate([0, 0, 180]) {
        clip();
    }
}
