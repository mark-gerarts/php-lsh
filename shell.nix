{ pkgs ? import <nixpkgs> {}}:

with pkgs;

mkShell rec {
  buildInputs = [
    php80
    php80Packages.composer
  ];
}
