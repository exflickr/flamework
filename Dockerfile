FROM ubuntu:14.04

COPY . /vagrant

WORKDIR /vagrant

ENTRYPOINT [ "/bin/bash", "tests/vagrant/init.sh" ]