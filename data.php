<?php

$testing_data = [[
[105, 95, 101, 102, 98, 94, 106, 107, 103, 100],
[107, 94, 100, 103, 96, 89, 108, 115, 105, 97],
[105, 97, 102, 103, 99, 95, 108, 110, 104, 100],
[104, 94, 98, 102, 95, 90, 107, 114, 103, 96],
[105, 96, 101, 102, 98, 94, 107, 109, 103, 100],
[92, 93, 96, 99, 104, 95, 106, 100, 110, 111],
[91, 92, 95, 97, 100, 93, 102, 98, 103, 104],
[91, 95, 97, 98, 101, 96, 102, 100, 103, 110],
[94, 96, 99, 100, 103, 97, 105, 101, 108, 112],
[93, 95, 97, 98, 102, 96, 103, 100, 104, 105],
[108, 114, 107, 105, 104, 99, 97, 103, 88, 90],
[106, 108, 105, 104, 101, 95, 94, 100, 92, 93],
[110, 111, 109, 108, 104, 100, 99, 102, 92, 98],
[106, 107, 103, 102, 101, 95, 94, 97, 92, 93],
[102, 112, 101, 100, 99, 96, 95, 97, 91, 92],
[93, 107, 95, 103, 94, 113, 96, 101, 98, 97],
[91, 104, 94, 101, 93, 109, 97, 100, 99, 98],
[89, 103, 91, 102, 90, 104, 94, 101, 100, 98],
[86, 106, 93, 102, 90, 108, 94, 99, 96, 95],
[84, 106, 90, 105, 87, 112, 96, 103, 102, 100],
[102, 101, 98, 96, 100, 97, 99, 94, 88, 95],
[101, 102, 98, 96, 100, 97, 99, 94, 88, 95],
[101, 98, 102, 96, 100, 97, 99, 94, 88, 95],
[101, 98, 96, 102, 100, 97, 99, 94, 88, 95],
[101, 98, 96, 100, 102, 97, 99, 94, 88, 95],
[106, 96, 102, 103, 99, 95, 107, 108, 104, 101],
[108, 95, 101, 104, 97, 90, 109, 116, 106, 98],
[106, 98, 103, 104, 100, 96, 109, 111, 105, 101],
[105, 95, 99, 103, 96, 91, 108, 115, 104, 97],
[106, 97, 102, 103, 99, 95, 108, 110, 104, 101],
[93, 94, 97, 100, 105, 96, 107, 101, 111, 112],
[92, 93, 96, 98, 101, 94, 103, 99, 104, 105],
[92, 96, 98, 99, 102, 97, 103, 101, 104, 111],
[95, 97, 100, 101, 104, 98, 106, 102, 109, 113],
[94, 96, 98, 99, 103, 97, 104, 101, 105, 106],
[109, 115, 108, 106, 105, 100, 98, 104, 89, 91],
[107, 109, 106, 105, 102, 96, 95, 101, 93, 94],
[111, 112, 110, 109, 105, 101, 100, 103, 93, 99],
[107, 108, 104, 103, 102, 96, 95, 98, 93, 94],
[103, 113, 102, 101, 100, 97, 96, 98, 92, 93],
[94, 108, 96, 104, 95, 114, 97, 102, 99, 98],
[92, 105, 95, 102, 94, 110, 98, 101, 100, 99],
[90, 104, 92, 103, 91, 105, 95, 102, 101, 99],
[87, 107, 94, 103, 91, 109, 95, 100, 97, 96],
[85, 107, 91, 106, 88, 113, 97, 104, 103, 101],
[103, 102, 99, 97, 101, 98, 100, 95, 89, 96],
[102, 103, 99, 97, 101, 98, 100, 95, 89, 96],
[102, 99, 103, 97, 101, 98, 100, 95, 89, 96],
[102, 99, 97, 103, 101, 98, 100, 95, 89, 96],
[102, 99, 97, 101, 103, 98, 100, 95, 89, 96]
], [
[135, 94, 113, 123, 102, 87, 150, 173, 132, 110],
[129, 104, 125, 127, 105, 101, 140, 149, 128, 112],
[128, 111, 115, 122, 115, 101, 137, 152, 126, 115],
[146, 105, 118, 119, 109, 98, 151, 159, 144, 115],
[130, 120, 125, 126, 121, 102, 134, 171, 127, 124],
[89, 100, 112, 117, 120, 108, 130, 119, 137, 148],
[86, 103, 114, 115, 123, 112, 124, 120, 131, 145],
[98, 106, 114, 116, 124, 109, 125, 118, 133, 149],
[98, 108, 115, 116, 128, 114, 133, 123, 141, 161],
[63, 80, 112, 116, 126, 105, 138, 121, 142, 178],
[126, 133, 122, 120, 113, 99, 97, 106, 95, 96],
[147, 150, 135, 132, 128, 125, 117, 127, 97, 99],
[138, 145, 128, 127, 124, 116, 112, 118, 104, 105],
[150, 151, 139, 134, 132, 129, 124, 131, 93, 104],
[127, 141, 126, 125, 117, 110, 102, 112, 95, 98],
[82, 152, 103, 140, 102, 159, 106, 128, 108, 107],
[93, 158, 98, 137, 95, 161, 111, 135, 132, 131],
[82, 142, 86, 123, 83, 152, 112, 122, 119, 117],
[87, 141, 107, 127, 101, 146, 108, 122, 121, 109],
[78, 138, 112, 136, 105, 153, 118, 122, 121, 119],
[161, 156, 118, 114, 128, 115, 127, 101, 99, 109],
[156, 161, 118, 114, 128, 115, 127, 101, 99, 109],
[156, 118, 161, 114, 128, 115, 127, 101, 99, 109],
[156, 118, 114, 161, 128, 115, 127, 101, 99, 109],
[156, 118, 114, 128, 161, 115, 127, 101, 99, 109],
[136, 95, 114, 124, 103, 88, 151, 174, 133, 111],
[130, 105, 126, 128, 106, 102, 141, 150, 129, 113],
[129, 112, 116, 123, 116, 102, 138, 153, 127, 116],
[147, 106, 119, 120, 110, 99, 152, 160, 145, 116],
[131, 121, 126, 127, 122, 103, 135, 172, 128, 125],
[90, 101, 113, 118, 121, 109, 131, 120, 138, 149],
[87, 104, 115, 116, 124, 113, 125, 121, 132, 146],
[99, 107, 115, 117, 125, 110, 126, 119, 134, 150],
[99, 109, 116, 117, 129, 115, 134, 124, 142, 162],
[64, 81, 113, 117, 127, 106, 139, 122, 143, 179],
[127, 134, 123, 121, 114, 100, 98, 107, 96, 97],
[148, 151, 136, 133, 129, 126, 118, 128, 98, 100],
[139, 146, 129, 128, 125, 117, 113, 119, 105, 106],
[151, 152, 140, 135, 133, 130, 125, 132, 94, 105],
[128, 142, 127, 126, 118, 111, 103, 113, 96, 99],
[83, 153, 104, 141, 103, 160, 107, 129, 109, 108],
[94, 159, 99, 138, 96, 162, 112, 136, 133, 132],
[83, 143, 87, 124, 84, 153, 113, 123, 120, 118],
[88, 142, 108, 128, 102, 147, 109, 123, 122, 110],
[79, 139, 113, 137, 106, 154, 119, 123, 122, 120],
[162, 157, 119, 115, 129, 116, 128, 102, 100, 110],
[157, 162, 119, 115, 129, 116, 128, 102, 100, 110],
[157, 119, 162, 115, 129, 116, 128, 102, 100, 110],
[157, 119, 115, 162, 129, 116, 128, 102, 100, 110],
[157, 119, 115, 129, 162, 116, 128, 102, 100, 110]
]];

?>
