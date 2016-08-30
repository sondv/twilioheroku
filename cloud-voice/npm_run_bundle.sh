echo "RUN BUNDLE SSSVVVVVVV nnnnnnnnn nnnnnnnnnnnnnn"
sudo cp public/manage/index.html dist/manage/
sudo cp public/share/index.html dist/share/
sudo sed -i -e "s/\"\/assets/\"\/\/assets.joincallwell.com/g" dist/manage/index.html dist/share/index.html

NODE_ENV=production webpack -p
