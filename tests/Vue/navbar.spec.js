import { createLocalVue, mount } from '@vue/test-utils';
import expect from 'expect';
import Navbar from '../../src/resources/js/Shared/Navbar'
import BootstrapVue from 'bootstrap-vue' //Importing
import moxios from 'moxios'

// create an extended `Vue` constructor
const localVue = createLocalVue();

// install plugins as normal
localVue.use(BootstrapVue);

describe('Navbar', () => {

  beforeEach(() => {
    moxios.install();
  });

  afterEach(() => {
    moxios.uninstall();
  });

  // pass the `localVue` to the mount options
  let wrapper = mount(Navbar, {
    localVue
  });

  it('should have loading truck icon if worker is running', function () {

    moxios.stubRequest('/queue/status', {
      status: 200,
      response: {
        "queue_count": 0,
        "error_count": 0,
        "status": "running"
      }
    });

    moxios.wait(function () {
      expect(wrapper.html()).toContain('<i class="fas fa-truck-loading"/>');
      done()
    })

  });

  it('should have pause icon if worker is paused', function () {

    moxios.stubRequest('/queue/status', {
      status: 200,
      response: {
        "queue_count": 0,
        "error_count": 0,
        "status": "paused"
      }
    });

    moxios.wait(function () {
      expect(wrapper.html()).toContain("<i class=\"fas fa-pause\"/>");
      done()
    })

  });
});
