Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu/trusty64"

  config.vm.synced_folder "www/templates_c/", "/vagrant/www/templates_c/", owner:"www-data", group:"www-data"

  config.vm.network :forwarded_port, guest: 80, host: 8080

  config.vm.provision :shell, :path => "build/vagrant/init.sh"

end
