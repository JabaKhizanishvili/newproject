function array_flip(a){var b,c={};if(a&&"object"==typeof a&&a.change_key_case)return a.flip();for(b in a)a.hasOwnProperty(b)&&(c[a[b]]=b);return c;}
