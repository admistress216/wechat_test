#include <stdio.h>
#include <stdlib.h>
void main() {
    typedef int Mar[10];
    Mar A = {1,2,3,4,5};
    int *p = A;
    printf("%d\n", p[2]);
}
