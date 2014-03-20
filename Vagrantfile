Vagrant.configure("2") do |config|
  config.vm.box = "precise32"
  config.vm.box_url = "http://files.vagrantup.com/precise32.box"

  config.vm.synced_folder "www/templates_c/", "/vagrant/www/templates_c/", owner:"www-data", group:"www-data"
  
  config.vm.network :forwarded_port, guest: 80, host: 8080

  config.vm.provision :shell, :path => "tests/vagrant/init.sh"
end
