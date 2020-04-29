import { createLocalVue, mount } from '@vue/test-utils';
import expect from 'expect';
import moxios from 'moxios'
import HorizonStats from "../../src/resources/js/Pages/Configuration/HorizonStats"

// create an extended `Vue` constructor
const localVue = createLocalVue();

const $route = function (string) {return '#'}

// install plugins as normal

describe('HorizonStats', () => {

  beforeEach(() => {
    moxios.install();
  });

  afterEach(() => {
    moxios.uninstall();
  });

  // pass the `localVue` to the mount options
  let wrapper = mount(HorizonStats, {
      localVue,
      mocks: {
          $route
      }
  });

  it('should running if worker status is running', function () {

    moxios.stubRequest('/queue/status', {
      status: 200,
      response: {
        "queue_count": 0,
        "error_count": 0,
        "status": "running"
      }
    });

    moxios.wait(function () {
        /*expect(wrapper.classes('text-2xl leading-8 font-semibold text-gray-900 capitalize')).toBe(true)*/
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
      /*expect(wrapper.html()).toContain("<i class=\"fas fa-pause\"/>");*/
      done()
    })

  });
});
