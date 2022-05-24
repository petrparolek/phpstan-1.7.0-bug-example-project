#!/usr/bin/env sh

dir=$(cd "${0%[/\\]*}" > /dev/null; cd '../tests' && pwd)

phpStan="vendor/bin/phpstan analyse $dir/../app $dir/../libs $dir/../tests -c $dir/../phpstan.neon";
echo $dir"/../$phpStan";

$dir/../$phpStan

r=$?

echo "exit code $r";
exit "$r";
