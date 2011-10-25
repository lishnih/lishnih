#!/usr/bin/env python
# coding=utf-8
# Stan 2011-10-25


__version__ = 0.1


def dotted(dotted_dict, separator='.'):
    new_dict = {}
    for key, value in dotted_dict.items():
        if separator in key:
            i = key.index(separator)
            newkey = key[:i]
            subkey = key[i+1:]
            sub = {}
            sub[subkey] = value
            new_dict.setdefault(newkey, {})
            new_dict[newkey].update(dotted(sub, separator=separator))
        else:
            new_dict[key] = value
    return new_dict


def todotted(todotted_dict, separator='.', level=None, up_dict=None, up_key=None):
    new_dict = {} if up_dict == None else up_dict
    if isinstance(todotted_dict, dict):
        for key, value in todotted_dict.items():
            new_key = "%s%s%s" % (up_key, separator, key) if up_key else key
            todotted(value, separator=separator, level=level, up_dict=new_dict, up_key=new_key)
    else:
        new_dict[up_key] = todotted_dict

    return new_dict if up_dict==None else 1



if __name__ == "__main__":


    print u"Пример использования функции dotted:"

    t = {}
    t['reports.report'] = ['G', 25]
    t['reports.joints'] = 5
    t[u'reports.стыки'] = u'пять'

    print u"Исходный dict"
    print t
    print u"Результат"
    t2 = dotted(t)
    print t2

    print u"Но ветки с разной вложенностью дадут непредсказуемый результат!"

    t['reports'] = 5

    print dotted(t)
    print


    print u"todotted возвращает в исходное состояние:"
    print todotted(t2)

    print u"Пустые ветки игнорируются!"

    t2['joints_null'] = {}

    print todotted(t2)

    t2['joints_null']['joints_null2'] = {}

    print todotted(t2)

    print u"Но!"

    t2['joints_null'] = {'joints_null2': ""}

    print todotted(t2, ':')
