require 'test_helper'

class LoungeControllerTest < ActionDispatch::IntegrationTest
  def setup
    @base_title = 'mr. R O B O T'
  end

  test 'should get contacts' do
    get lounge_contacts_url
    assert_response :success
    assert_select 'title', "contacts | #{@base_title}"
  end

  test 'should get main' do
    get lounge_main_url
    assert_response :success
    assert_select 'title', @base_title
  end
end
