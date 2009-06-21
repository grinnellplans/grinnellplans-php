#!/usr/bin/ruby
# GrinnellPlans posting script v0.2
# Updated: 6/25/2006
# Author: Will Emigh (will@studiocypher.com)
# This program is provided as-is under an MIT license, although I would like to hear from you if you do
#  something fun with it.
# Updated by [wellons]:
#   use username instead of userid
#   to work with PHP Sessions 
#   read update in from a file
#   there's some bug in plans that makes us do more work for unix line-endings
#   set user-agent


require 'net/http'
require 'cgi'

if ARGV[0].nil? || ARGV[1].nil? || ARGV[2].nil?
  print "Usage: file-based-post username password file-containing-update\n"
  print "This script prepends your plan with the date and the "
  print "contents of the given file.\n"
  exit
end

update_file = ARGV[2]
update_file_handle = File.new(update_file, "r")
update = update_file_handle.read()

username = ARGV[0]
password = CGI::escape(ARGV[1])
cookie = ''

Net::HTTP.start('www.grinnellplans.com') do |http|
  path = '/index.php'
  data = "username=#{username}&password=#{password}"
  headers = {
	'User-Agent' => "#{$0} #{$*}: version 0.2",
  	'Content-Type' => 'application/x-www-form-urlencoded'
  }
  resp, data = http.post(path, data, headers)
  cookie = resp['Set-Cookie']


  req = Net::HTTP::Get.new("/edit.php")
  req['Cookie'] = cookie
  response = http.request(req)
  b = response.body.gsub(/[\n\r]/,'<br />')
  entry = b.scan(/<textarea rows=\"14\" cols=\"70\" <br \/>name=\"plan\" wrap=\"virtual\" onkeyup=\"javascript:countlen\(\);\">(.*)<\/textarea>/)
  @old_plan = entry[0][0]
  @old_plan.gsub!(/<br \/>/,"\n\r")

  # Fix unix linebreaks which get removed the second time you view the plan in edit.php
  update.gsub!(/\n([^\r])/,"\n\r\\1")
  new_plan = "[date]\n\r"+update+"\n\r\n\r"+CGI::unescapeHTML(@old_plan)
  new_plan = CGI::escape(new_plan)
  
  path = '/edit.php'
  data = "plan=#{new_plan}&part=1"
  headers = {
    'Content-Type' => 'application/x-www-form-urlencoded'
  }
  headers['Cookie'] = cookie
  
  # Make the post request
  resp, data = http.post(path, data, headers)
end

print "Update posted.\n"
